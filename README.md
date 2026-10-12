# Hairline Beauty Lounge - Core Backend Engine

## 📌 Overview
This repository contains the core backend infrastructure and smart booking engine for **Hairline Beauty Lounge**. The system is architected to seamlessly manage dual-operational models: the physical salon located in Al-Maseef (Riyadh) and the targeted Home Service operations across 12 designated neighborhoods[cite: 34]. 

Designed by **Abdulhakim (System Architect)**, the architecture prioritizes scalability, high availability, and operational automation, utilizing a robust tech stack capable of handling complex business logic, dynamic pricing, and real-time logistics.

## 🏗️ System Architecture & Key Modules

### 1. Smart Scheduling & Booking Engine
*   **Dynamic Time Allocation:** Algorithmically calculates service duration based on multiple variables (e.g., hair length constraints: V. Short to XL)[cite: 23].
*   **Conflict Resolution:** Prevents booking overlaps while allowing sequential multi-service bookings for a single client with different experts[cite: 34].
*   **Quiet Hours Optimization:** Automated yield management to push specific packages during low-traffic hours[cite: 31].

### 2. Home Service Logistics (Fleet Management)
*   **Geospatial Filtering:** Restricts home service bookings to authorized neighborhoods (e.g., Al-Taawun, Al-Izdihar, Al-Muruj, etc.)[cite: 34].
*   **Minimum Order Value (MOV):** Enforces minimum cart value and calculates dynamic transportation fees based on the client's zone[cite: 34].

### 3. Financial & Omnichannel Payment Integration
*   **Automated Deposits:** Logic to enforce a 20%-30% deposit rule to confirm bookings, integrated with cancellation policies (24-hour rule)[cite: 34].
*   **Payment Gateways:** Architected to support Mada, Visa, Mastercard, Apple Pay, and BNPL services (Tabby/Tamara)[cite: 34].

### 4. Role-Based Access Control (RBAC) & Dashboards
*   **Admin Console:** Powered by FilamentPHP for full oversight of daily operations, financial reports, and client retention metrics.
*   **Expert/Staff Interface:** Tailored views for salon staff to track upcoming appointments and client history.

## 🛠️ Tech Stack
*   **Framework:** Laravel 11.x (API-First approach)
*   **Admin Panel:** FilamentPHP (v3)
*   **Database:** PostgreSQL / MySQL (Optimized for complex relational scheduling queries)
*   **Queue Management:** Redis (For handling automated SMS reminders and background jobs)

## 🚀 Execution Phases (Scope of Work)

To ensure rapid deployment and business continuity, the development is divided into strategic phases:

### Phase 1: MVP & Core Booking Engine (Current Phase)
- Database schema design and relationship mapping.
- Core booking APIs (Salon + Home Service).
- Dynamic pricing logic implementation based on service catalogs[cite: 16, 24].
- FilamentPHP Admin Dashboard for schedule management.
- Integration with primary payment gateway for mandatory deposits.

### Phase 2: Logistics & Third-Party Integrations (Future Scope)
- Driver tracking API integration (Live Maps).
- Buy-Now-Pay-Later (BNPL) integrations (Tabby/Tamara).
- Advanced CRM & Referral Program engine (e.g., "Share Hairline" logic)[cite: 32].
- Integration with external ERPs (e.g., Odoo) for inventory and advanced accounting[cite: 34].

## 🔒 Security & Compliance
*   Strict validation rules for all client endpoints.
*   Data encryption for client personal information in compliance with local regulations.
*   Pessimistic locking implemented on booking time slots to prevent race conditions during high traffic.

---
*Architected for Hairline Beauty Lounge by Hussein Al-Mezher Consulting.*
