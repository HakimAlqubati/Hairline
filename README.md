# Hairline Beauty Lounge - Core Backend Engine

## 📌 Overview
This repository contains the core backend infrastructure and smart booking engine for **Hairline Beauty Lounge**. The system is architected to seamlessly manage dual-operational models: the physical salon located in Al-Maseef (Riyadh)[cite: 26, 34] and the targeted Home Service operations across 12 designated neighborhoods. 

Designed by **Abdulhakim (System Architect)**, the architecture prioritizes scalability, high availability, and operational automation, utilizing a robust tech stack capable of handling complex business logic, dynamic pricing, and real-time logistics.

## 🏗️ System Architecture & Key Modules

### 1. Smart Scheduling & Booking Engine
*   **Dynamic Time Allocation:** Algorithmically calculates service duration based on multiple variables, including hair length constraints (V. Short to XL)[cite: 23].
*   **Conflict Resolution:** Prevents booking overlaps while allowing sequential multi-service bookings for a single client with different experts.
*   **Quiet Hours Optimization:** Automated yield management logic designed to push specific packages during low-traffic hours[cite: 31].

### 2. Home Service Logistics (Fleet Management)
*   **Geospatial Filtering:** Restricts home service bookings to authorized neighborhoods (e.g., Al-Taawun, Al-Izdihar, Al-Muruj)[cite: 34].
*   **Minimum Order Value (MOV):** Enforces minimum cart value constraints and calculates dynamic transportation fees based on the client's geographic zone[cite: 34].

### 3. Financial & Omnichannel Payment Integration
*   **Automated Deposits:** Logic to enforce a 20%-30% deposit rule to confirm bookings, integrated with strict 24-hour cancellation policies[cite: 34].
*   **Payment Gateways:** Architected to support Mada, Visa, Mastercard, Apple Pay, and BNPL services like Tabby and Tamara[cite: 34].

### 4. Role-Based Access Control (RBAC) & Dashboards
*   **Admin Console:** Powered by FilamentPHP for full oversight of daily operations, financial reports, and client retention metrics.
*   **Expert/Staff Interface:** Tailored views for salon staff to track upcoming appointments, service details, and client history[cite: 34].

## 🛠️ Tech Stack
*   **Framework:** Laravel 13.x (API-First approach)
*   **Admin Panel:** FilamentPHP (v3)
*   **Database:** PostgreSQL / MySQL (Optimized for complex relational scheduling queries)
*   **Queue Management:** Redis (For handling automated SMS reminders and background background jobs)

## 🚀 Execution Phases (Scope of Work)

To ensure rapid deployment, risk mitigation, and business continuity, the development is divided into strategic phases:

### Phase 1: MVP & Core Booking Engine (Current Phase)
- Database schema design and multi-relational mapping.
- Core booking APIs (Salon + Home Service).
- Dynamic pricing logic implementation based on approved service catalogs[cite: 16, 24].
- FilamentPHP Admin Dashboard for centralized schedule management.
- Integration with primary payment gateways (Visa/Mada/Apple Pay) for mandatory deposits[cite: 34].

### Phase 2: Logistics & Advanced Integrations (Future Scope)
- Driver tracking API integration (Live Maps) for Home Services[cite: 34].
- Buy-Now-Pay-Later (BNPL) integrations (Tabby/Tamara)[cite: 34].
- Advanced CRM & Referral Program engine (e.g., "Share Hairline" logic)[cite: 32].
- Integration with external ERPs (e.g., Odoo) for inventory and advanced accounting[cite: 34].

## 🔒 Security & Compliance
*   Strict API validation rules for all client endpoints.
*   Data encryption for client personal information in compliance with local regulations.
*   Pessimistic locking implemented on booking time slots to prevent race conditions during high concurrent traffic.

---
*Architected for Hairline Beauty Lounge by Hussein Al-Mezher Consulting.*
