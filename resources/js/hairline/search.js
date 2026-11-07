import { unifiedSearchConfig, state, routes } from './state.js';
import { setCategoryExpandedState, expandAllBookingCategories, syncToggleAllTreeButton } from './booking-wizard.js';

export function normalizeSearchText(str) {
    if (!str) return '';
    const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let out = String(str).toLowerCase();
    for (let i = 0; i < eastern.length; i++) {
        out = out.split(eastern[i]).join(western[i]);
    }
    return out
        .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
        .replace(/[أإآٱ]/g, 'ا')
        .replace(/ة/g, 'ه')
        .replace(/ى/g, 'ي')
        .replace(/ؤ/g, 'و')
        .replace(/ئ/g, 'ي')
        .replace(/\s+/g, ' ')
        .trim();
}

export function matchesSearchTokens(haystackText, normalizedQuery) {
    if (!normalizedQuery) return true;
    const normalizedHaystack = normalizeSearchText(haystackText);
    const tokens = normalizedQuery.split(' ').filter(Boolean);
    return tokens.every(token => normalizedHaystack.includes(token));
}

export function setServicesSearchLoading(isLoading) {
    const spinner = document.getElementById('servicesSearchSpinner');
    const icon = document.getElementById('servicesSearchIcon');
    if (spinner && icon) {
        if (isLoading) {
            spinner.classList.remove('hidden');
            spinner.style.display = 'flex';
            icon.classList.add('hidden');
            icon.style.display = 'none';
        } else {
            spinner.classList.add('hidden');
            spinner.style.display = 'none';
            icon.classList.remove('hidden');
            icon.style.display = 'flex';
        }
    }
}

export function hideServicesSuggestions() {
    setServicesSearchLoading(false);
    const dropdown = document.getElementById('servicesSuggestionsDropdown');
    const backdrop = document.getElementById('servicesSuggestionsBackdrop');
    if (dropdown) dropdown.classList.add('hidden');
    if (backdrop) backdrop.classList.add('hidden');
}

export function getClientCatalogServices() {
    if (state.cachedCatalogServices && state.cachedCatalogServices.length > 0) {
        return state.cachedCatalogServices;
    }

    const services = [];
    const pages = document.querySelectorAll('#servicesTabView .menu-page');

    pages.forEach(page => {
        const mainTitleEl = page.querySelector(':scope > .page-content > .section-main-title');
        const pageTitleText = mainTitleEl ? (mainTitleEl.querySelector('.ar-title')?.textContent?.trim() || mainTitleEl.textContent?.trim()) : '';

        // Multi-length blocks
        page.querySelectorAll('.multi-length-block').forEach(block => {
            const subGroup = block.closest('.service-subsection-group');
            const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
            const blockArTitle = block.querySelector('.multi-length-title .ar-name')?.textContent?.trim() || '';
            const blockEnTitle = block.querySelector('.multi-length-title .en-name')?.textContent?.trim() || '';
            const categoryLabel = [pageTitleText, subTitleText].filter(Boolean).join(' • ');

            block.querySelectorAll('.length-row-item.service-row').forEach(row => {
                const lenAr = row.querySelector('.length-label .ar')?.textContent?.trim() || '';
                const lenEn = row.querySelector('.length-label .en')?.textContent?.trim() || '';
                const name = blockArTitle ? `${blockArTitle} (${lenAr})` : (row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim());
                const name_en = blockEnTitle ? `${blockEnTitle} (${lenEn})` : (row.querySelector('.en-name')?.textContent?.trim() || '');
                const price_display = row.querySelector('.price-badge')?.textContent?.trim() || '';

                if (name) {
                    services.push({
                        name,
                        name_en,
                        category: categoryLabel,
                        price_display,
                    });
                }
            });
        });

        // Standard service rows
        page.querySelectorAll('.service-row:not(.length-row-item)').forEach(row => {
            const subGroup = row.closest('.service-subsection-group');
            const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
            const catGroup = row.closest('.category-group');
            const catLabelText = catGroup?.querySelector('.category-side-label')?.textContent?.trim() || '';
            const categoryLabel = [pageTitleText, catLabelText, subTitleText].filter(Boolean).join(' • ');

            const name = row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim();
            const name_en = row.querySelector('.en-name')?.textContent?.trim() || '';
            const price_display = row.querySelector('.price-badge')?.textContent?.trim() || '';

            if (name) {
                services.push({
                    name,
                    name_en,
                    category: categoryLabel,
                    price_display,
                });
            }
        });
    });

    state.cachedCatalogServices = services;
    return services;
}

export function getClientSideSuggestions(query, limit = 10) {
    const normalizedQuery = normalizeSearchText(query);
    if (!normalizedQuery) return [];

    const all = getClientCatalogServices();
    const matched = [];

    for (const s of all) {
        const text = `${s.category} ${s.name} ${s.name_en}`;
        if (matchesSearchTokens(text, normalizedQuery)) {
            matched.push(s);
            if (matched.length >= limit) break;
        }
    }
    return matched;
}

export function handleSearchFocus(context) {
    if (context === 'services') {
        const inputEl = document.getElementById('serviceSearch');
        if (inputEl && inputEl.value.trim().length > 0) {
            handleUnifiedSearchInput('services');
        }
    }
}

export function handleUnifiedSearchInput(context) {
    const cfg = unifiedSearchConfig[context];
    if (!cfg) return;

    const inputEl = document.getElementById(cfg.inputId);
    const clearBtn = document.getElementById(cfg.clearBtnId);
    if (!inputEl) return;

    const trimmedVal = inputEl.value.trim();
    const hasText = trimmedVal.length > 0;
    if (clearBtn) {
        clearBtn.classList.toggle('hidden', !hasText);
    }

    if (context === 'services') {
        if (hasText) {
            // 1. Instant 0ms client-side suggestions for immediate feedback
            const clientMatches = getClientSideSuggestions(trimmedVal, 10);
            if (clientMatches.length > 0) {
                renderServiceSuggestions(clientMatches, trimmedVal);
            }

            // 2. Fetch server suggestions in background for ranked/normalized suggestions
            fetchServiceAjaxSuggestions(trimmedVal);
        } else {
            if (state.serviceSuggestionsDebounceTimer) {
                clearTimeout(state.serviceSuggestionsDebounceTimer);
            }
            if (state.serviceSuggestionsAbortController) {
                state.serviceSuggestionsAbortController.abort();
            }
            hideServicesSuggestions();
            executeUnifiedServiceSearch(context);
        }
        return;
    }

    if (!hasText) {
        executeUnifiedServiceSearch(context);
    }
}

export function fetchServiceAjaxSuggestions(query) {
    const dropdown = document.getElementById('servicesSuggestionsDropdown');
    const backdrop = document.getElementById('servicesSuggestionsBackdrop');
    const listEl = document.getElementById('servicesSuggestionsList');
    if (!dropdown || !listEl) return;

    if (state.serviceSuggestionsDebounceTimer) {
        clearTimeout(state.serviceSuggestionsDebounceTimer);
    }

    const cleanQuery = (query || '').trim();
    if (cleanQuery.length === 0) {
        hideServicesSuggestions();
        listEl.innerHTML = '';
        return;
    }

    state.serviceSuggestionsDebounceTimer = setTimeout(() => {
        if (state.serviceSuggestionsAbortController) {
            state.serviceSuggestionsAbortController.abort();
        }
        state.serviceSuggestionsAbortController = new AbortController();

        const currentVal = document.getElementById(unifiedSearchConfig.services.inputId)?.value?.trim();
        if (currentVal !== cleanQuery) {
            setServicesSearchLoading(false);
            return;
        }

        setServicesSearchLoading(true);

        const suggestionsUrl = routes.suggestions || window.__HAIRLINE_DATA__?.routes?.suggestions || '/services/suggestions';

        fetch(`${suggestionsUrl}?q=${encodeURIComponent(cleanQuery)}&limit=10`, {
            signal: state.serviceSuggestionsAbortController.signal,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            setServicesSearchLoading(false);

            const latestInputVal = document.getElementById(unifiedSearchConfig.services.inputId)?.value?.trim();
            if (latestInputVal !== cleanQuery) return;

            if (!data.success || !Array.isArray(data.suggestions) || data.suggestions.length === 0) {
                // If client already displayed results, don't wipe them out
                if (listEl.children.length === 0) {
                    renderNoServiceMatchNotice(cleanQuery);
                }
                return;
            }

            renderServiceSuggestions(data.suggestions, cleanQuery);
        })
        .catch(err => {
            setServicesSearchLoading(false);
            if (err.name !== 'AbortError') {
                if (listEl.children.length === 0) {
                    renderNoServiceMatchNotice(cleanQuery);
                }
            }
        });
    }, 150);
}

export function renderNoServiceMatchNotice(query) {
    const dropdown = document.getElementById('servicesSuggestionsDropdown');
    const backdrop = document.getElementById('servicesSuggestionsBackdrop');
    const listEl = document.getElementById('servicesSuggestionsList');
    if (!dropdown || !listEl) return;

    const safeQuery = (query || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    listEl.innerHTML = `
        <div class="py-3 px-4 text-center select-none">
            <p class="text-xs font-bold text-sage-dark">لا توجد خدمات مطابقة لـ «<span class="text-primary-dark font-extrabold">${safeQuery}</span>»</p>
        </div>
    `;
    dropdown.classList.remove('hidden');
    if (backdrop) backdrop.classList.remove('hidden');
}

export function renderServiceSuggestions(suggestions, query) {
    const dropdown = document.getElementById('servicesSuggestionsDropdown');
    const backdrop = document.getElementById('servicesSuggestionsBackdrop');
    const listEl = document.getElementById('servicesSuggestionsList');
    if (!dropdown || !listEl) return;

    listEl.innerHTML = suggestions.map((item, index) => {
        const highlightedName = highlightSearchMatch(item.name, query);
        const safeName = (item.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        return `
            <button type="button"
                    onclick="selectServiceSuggestion('${safeName}')"
                    onmousedown="event.preventDefault(); selectServiceSuggestion('${safeName}')"
                    class="service-suggestion-item w-full text-right p-3 rounded-xl hover:bg-[#FAF6F0] focus:bg-[#FAF6F0] border border-transparent hover:border-gold-soft/60 flex items-center justify-between gap-3 transition-all cursor-pointer group outline-none"
                    data-index="${index}">
                <div class="min-w-0 flex items-center gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary shrink-0 transition-colors"></span>
                    <div class="min-w-0">
                        ${item.category ? `<span class="text-[11px] font-bold text-gold-dark block truncate mb-0.5">${item.category}</span>` : ''}
                        <div class="font-extrabold text-sm text-sage-dark group-hover:text-primary-dark transition-colors truncate">
                            ${highlightedName}
                        </div>
                        ${item.name_en ? `<span class="text-[10px] font-serif tracking-wider text-gray-400 block uppercase truncate mt-0.5">${item.name_en}</span>` : ''}
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2 mr-2">
                    <span class="px-3 py-1 rounded-full bg-gold-light/70 text-gold-dark font-extrabold text-xs border border-gold/30 shadow-2xs whitespace-nowrap">
                        ${item.price_display}
                    </span>
                    <i class="fa-solid fa-chevron-left text-[11px] text-gray-300 group-hover:text-primary transition-transform group-hover:-translate-x-1 shrink-0"></i>
                </div>
            </button>
        `;
    }).join('');

    dropdown.classList.remove('hidden');
    if (backdrop) backdrop.classList.remove('hidden');
}

export function highlightSearchMatch(text, query) {
    if (!query) return text;
    const normalizedText = normalizeSearchText(text);
    const normalizedQuery = normalizeSearchText(query);
    const idx = normalizedText.indexOf(normalizedQuery);
    if (idx === -1) return text;

    const before = text.substring(0, idx);
    const match = text.substring(idx, idx + query.length);
    const after = text.substring(idx + query.length);
    return `${before}<mark class="bg-primary/20 text-primary-dark font-black rounded px-1">${match}</mark>${after}`;
}

export function selectServiceSuggestion(serviceName) {
    if (state.serviceSuggestionsDebounceTimer) {
        clearTimeout(state.serviceSuggestionsDebounceTimer);
    }
    if (state.serviceSuggestionsAbortController) {
        state.serviceSuggestionsAbortController.abort();
    }

    hideServicesSuggestions();
    setServicesSearchLoading(false);

    const cfg = unifiedSearchConfig['services'];
    const inputEl = document.getElementById(cfg.inputId);

    if (inputEl) {
        inputEl.value = serviceName;
        const clearBtn = document.getElementById(cfg.clearBtnId);
        if (clearBtn) clearBtn.classList.remove('hidden');
    }

    executeUnifiedServiceSearch('services');
}

export function executeUnifiedServiceSearch(context, event = null) {
    if (event) {
        event.preventDefault();
    }

    if (state.serviceSuggestionsDebounceTimer) {
        clearTimeout(state.serviceSuggestionsDebounceTimer);
    }
    if (state.serviceSuggestionsAbortController) {
        state.serviceSuggestionsAbortController.abort();
    }

    hideServicesSuggestions();
    setServicesSearchLoading(false);

    const cfg = unifiedSearchConfig[context];
    if (!cfg) return;

    const inputEl = document.getElementById(cfg.inputId);
    const clearBtn = document.getElementById(cfg.clearBtnId);
    if (!inputEl) return;

    const rawQuery = inputEl.value.trim();
    const normalizedQuery = normalizeSearchText(rawQuery);

    if (clearBtn) {
        clearBtn.classList.toggle('hidden', rawQuery.length === 0);
    }

    if (event && document.activeElement === inputEl) {
        inputEl.blur();
    }

    if (context === 'services') {
        filterServicesCatalogView(rawQuery, normalizedQuery);
    } else if (context === 'booking') {
        filterBookingTreeView(rawQuery, normalizedQuery);
    }
}

export function clearUnifiedServiceSearch(context, focusInput = false) {
    if (state.serviceSuggestionsDebounceTimer) {
        clearTimeout(state.serviceSuggestionsDebounceTimer);
    }
    if (state.serviceSuggestionsAbortController) {
        state.serviceSuggestionsAbortController.abort();
    }

    hideServicesSuggestions();
    setServicesSearchLoading(false);

    const cfg = unifiedSearchConfig[context];
    if (!cfg) return;

    const inputEl = document.getElementById(cfg.inputId);
    const clearBtn = document.getElementById(cfg.clearBtnId);

    if (inputEl) {
        inputEl.value = '';
        if (focusInput) inputEl.focus();
    }
    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }

    if (context === 'services') {
        filterServicesCatalogView('', '');
    } else if (context === 'booking') {
        filterBookingTreeView('', '');
    }
}

export function updateSearchFeedbackUI(context, rawQuery, normalizedQuery, matchedCount) {
    const cfg = unifiedSearchConfig[context];
    if (!cfg) return;

    const statusEl = document.getElementById(cfg.statusId);
    const countEl = document.getElementById(cfg.countId);
    const queryTextEl = document.getElementById(cfg.queryTextId);
    const emptyStateEl = document.getElementById(cfg.emptyStateId);
    const emptyQueryEl = document.getElementById(cfg.emptyQueryId);

    if (!normalizedQuery) {
        if (statusEl) {
            statusEl.classList.add('hidden');
            statusEl.classList.remove('flex');
        }
        if (emptyStateEl) {
            emptyStateEl.classList.add('hidden');
        }
        return;
    }

    if (queryTextEl) queryTextEl.textContent = rawQuery;
    if (countEl) countEl.textContent = String(matchedCount);
    if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.classList.add('flex');
    }

    if (emptyQueryEl) emptyQueryEl.textContent = rawQuery;
    if (emptyStateEl) {
        emptyStateEl.classList.toggle('hidden', matchedCount > 0);
    }
}

export function filterServicesCatalogView(rawQuery, normalizedQuery) {
    const pagesContainer = document.getElementById('pagesContainer');
    const resultsContainer = document.getElementById('servicesSearchResultsContainer');
    const resultsList = document.getElementById('servicesSearchResultsList');

    if (!normalizedQuery) {
        if (pagesContainer) pagesContainer.classList.remove('hidden');
        if (resultsContainer) resultsContainer.classList.add('hidden');
        if (resultsList) resultsList.innerHTML = '';
        updateSearchFeedbackUI('services', '', '', 0);
        return;
    }

    const pages = document.querySelectorAll('#servicesTabView .menu-page');
    const matchedItems = [];

    pages.forEach(page => {
        const mainTitleEl = page.querySelector(':scope > .page-content > .section-main-title');
        const pageTitleText = mainTitleEl ? (mainTitleEl.querySelector('.ar-title')?.textContent?.trim() || mainTitleEl.textContent?.trim()) : '';

        // 1. Multi-length service blocks
        page.querySelectorAll('.multi-length-block').forEach(block => {
            const subGroup = block.closest('.service-subsection-group');
            const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
            const blockArTitle = block.querySelector('.multi-length-title .ar-name')?.textContent?.trim() || '';
            const blockEnTitle = block.querySelector('.multi-length-title .en-name')?.textContent?.trim() || '';
            const categoryLabel = [pageTitleText, subTitleText].filter(Boolean).join(' • ');

            block.querySelectorAll('.length-row-item.service-row').forEach(row => {
                const lenAr = row.querySelector('.length-label .ar')?.textContent?.trim() || '';
                const lenEn = row.querySelector('.length-label .en')?.textContent?.trim() || '';
                const arName = blockArTitle ? `${blockArTitle} (${lenAr})` : (row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim());
                const enName = blockEnTitle ? `${blockEnTitle} (${lenEn})` : (row.querySelector('.en-name')?.textContent?.trim() || '');
                const price = row.querySelector('.price-badge')?.textContent?.trim() || '';
                const combinedText = `${categoryLabel} ${blockArTitle} ${lenAr} ${blockEnTitle} ${lenEn} ${price}`;

                if (matchesSearchTokens(combinedText, normalizedQuery)) {
                    matchedItems.push({
                        arName,
                        enName,
                        category: categoryLabel,
                        price,
                    });
                }
            });
        });

        // 2. Standard service rows
        page.querySelectorAll('.service-row:not(.length-row-item)').forEach(row => {
            const subGroup = row.closest('.service-subsection-group');
            const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
            const catGroup = row.closest('.category-group');
            const catLabelText = catGroup?.querySelector('.category-side-label')?.textContent?.trim() || '';
            const categoryLabel = [pageTitleText, catLabelText, subTitleText].filter(Boolean).join(' • ');

            const arName = row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim();
            const enName = row.querySelector('.en-name')?.textContent?.trim() || '';
            const price = row.querySelector('.price-badge')?.textContent?.trim() || '';
            const combinedText = `${categoryLabel} ${arName} ${enName} ${price}`;

            if (matchesSearchTokens(combinedText, normalizedQuery)) {
                matchedItems.push({
                    arName,
                    enName,
                    category: categoryLabel,
                    price,
                });
            }
        });
    });

    const totalMatchedServices = matchedItems.length;

    if (totalMatchedServices > 0) {
        if (pagesContainer) pagesContainer.classList.add('hidden');
        if (resultsContainer) resultsContainer.classList.remove('hidden');
        if (resultsList) {
            resultsList.innerHTML = matchedItems.map(item => `
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-primary-soft/80 shadow-xs flex items-center justify-between gap-3 hover:border-primary transition">
                    <div class="min-w-0">
                        ${item.category ? `<span class="text-[11px] font-bold text-gold-dark block truncate mb-0.5">${item.category}</span>` : ''}
                        <h4 class="font-extrabold text-sm sm:text-[15px] text-sage-dark leading-snug">${item.arName}</h4>
                        ${item.enName ? `<span class="text-[11px] font-serif tracking-wider text-gray-400 block uppercase mt-0.5">${item.enName}</span>` : ''}
                    </div>
                    ${item.price ? `
                        <div class="shrink-0 text-left">
                            <span class="inline-block px-3 py-1 rounded-full bg-gold-light/60 text-gold-dark font-extrabold text-xs sm:text-sm border border-gold/25 shadow-2xs">
                                ${item.price}
                            </span>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }
    } else {
        if (pagesContainer) pagesContainer.classList.add('hidden');
        if (resultsContainer) resultsContainer.classList.add('hidden');
        if (resultsList) resultsList.innerHTML = '';
    }

    updateSearchFeedbackUI('services', rawQuery, normalizedQuery, totalMatchedServices);
}

export function filterBookingTreeView(rawQuery, normalizedQuery) {
    const categories = document.querySelectorAll('#bookingStep1Container .booking-tree-category');

    if (!normalizedQuery) {
        categories.forEach(catEl => {
            catEl.classList.remove('hidden');
            const countBadge = catEl.querySelector('.booking-cat-count');
            if (countBadge) {
                const total = countBadge.getAttribute('data-total-count') || '0';
                countBadge.textContent = `${total} خدمات`;
            }
        });
        document.querySelectorAll('#bookingStep1Container .booking-service-item').forEach(li => {
            li.classList.remove('hidden');
        });
        updateSearchFeedbackUI('booking', '', '', 0);
        expandAllBookingCategories();
        return;
    }

    let totalMatchedBookingServices = 0;

    categories.forEach(catEl => {
        const catId = catEl.getAttribute('data-category-tree');
        const catTitle = catEl.getAttribute('data-category-title') || '';
        const items = catEl.querySelectorAll('.booking-service-item');
        let catMatches = 0;

        items.forEach(li => {
            const combinedText = `${catTitle} ${li.textContent}`;
            if (matchesSearchTokens(combinedText, normalizedQuery)) {
                li.classList.remove('hidden');
                catMatches++;
                totalMatchedBookingServices++;
            } else {
                li.classList.add('hidden');
            }
        });

        const countBadge = catEl.querySelector('.booking-cat-count');
        if (catMatches > 0) {
            catEl.classList.remove('hidden');
            if (catId) setCategoryExpandedState(catId, true);
            if (countBadge) {
                countBadge.textContent = `${catMatches} خدمات`;
            }
        } else {
            catEl.classList.add('hidden');
        }
    });

    updateSearchFeedbackUI('booking', rawQuery, normalizedQuery, totalMatchedBookingServices);
    syncToggleAllTreeButton();
}

export function initServicesSearch() {
    // Outside click dismisses suggestions dropdown safely
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('servicesSuggestionsDropdown');
        const searchForm = document.getElementById('serviceSearch')?.closest('form');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if ((!searchForm || !searchForm.contains(e.target)) && !dropdown.contains(e.target)) {
                hideServicesSuggestions();
            }
        }
    });

    // Close suggestions dropdown when pressing Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            hideServicesSuggestions();
        }
    });
}
