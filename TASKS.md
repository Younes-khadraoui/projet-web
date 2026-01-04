# Project Tasks Checklist

This document tracks the completion status of the features required by the project specification.

## ✅ Completed Tasks

### Core Functionality

- [x] **User Authentication:** Full system for registration, login, and logout.
- [x] **Ad Creation:** Users can post ads with a title (5-30 chars), description (5-200 chars), price, category, and delivery modes.
- [x] **Photo Uploads:** Users can upload up to 5 JPEG images (max 200KB each) for an ad.
- [x] **Ad Deletion:** Users can delete their own ads if they have not been sold.
- [x] **Homepage:** Displays a list of categories with ad counts and the 4 most recent ads.
- [x] **Category Pages:** Ads are displayed in a paginated list (10 per page) for each category.
- [x] **Ad Detail Page:** Shows all ad details, including all photos, price, description, and delivery options.
- [x] **Buying System:** Registered users can buy an item, which marks it as "sold" and removes it from public sale.
- [x] **User Dashboard:** Provides three distinct lists for the user: "Mes Annonces" (for sale), "Vendues" (sold by them), and "Mes Achats" (purchased by them).
- [x] **Confirm Receipt:** Buyers can confirm they have received an item from their dashboard.

### Administration

- [x] **Full Admin Panel:** An interface for administrators to manage the platform.
- [x] **Delete Any Ad:** Admins can delete any ad.
- [x] **Delete Any User:** Admins can delete a user, which also deletes all of their ads.
- [x] **Category Management:** Admins can add and rename categories.

### Deployment

- [x] **Web-Based Installer:** A user-friendly installer at `/install` that configures the database and creates the `.env` file automatically, making the project "clé en main" (turnkey).
- [x] **Automated DB Setup:** The installer runs the `schema.sql` and `data.sql` scripts.

### Enhancements (Beyond Core Requirements)

- [x] **Payment System:** A full balance system where money is transferred from the buyer's account to the seller's account.
- [x] **Balance Top-Up:** Users can add funds to their account via a modal accessible from the navigation bar.

---

## ❌ To-Do / Pending Tasks

### Core Functionality

- [ ] **Improve Login Redirect:** When an anonymous user tries a restricted action (like buying), they are sent to the login page. The spec suggests that after logging in, they should be returned to the action they were trying to perform.

  - _Current behavior: The user is redirected to the homepage after login._
  - _Required change: Implement session logic to store the intended URL and redirect back to it after a successful login._

- [x] **Implement Search Functionality:** The spec mentions that buyers use the platform to "effectuer des recherches," but there is no search bar or search functionality yet.
  - _Required change: Add a search bar to the header or homepage and create a new action (`?action=search`) to handle search queries and display results._

### User Interface & Experience

- [ ] **Improve Photo Gallery:** The ad detail page lists photos, but a more interactive viewer (like a lightbox or a modal that opens when a thumbnail is clicked) would better fulfill the "moyen de visionner toutes les photos" requirement.

### Documentation

- [ ] **Final Report (`rapport.pdf`):** A final report needs to be written that details:
  - The MVC architecture of the application.
  - Security measures taken (e.g., password hashing, preventing XSS, SQL injection).
  - Ergonomic and design choices.
