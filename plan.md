# Plan: Lost and Damaged Book Tracking with Accountability

## Goal
Implement a system to track books that are lost or damaged and automatically link them to the last borrower for accountability.

---

## Phase 1: Database Schema Updates
Update the existing tables to support new statuses.

### 1.1 Update `books` table
- Modify `availability` enum to include `damaged` and `lost`.
- **SQL**: `ALTER TABLE books MODIFY COLUMN availability ENUM('available', 'borrowed', 'damaged', 'lost') DEFAULT 'available';`

### 1.2 Update `borrow_transaction_items` table
- Modify `status` enum to include `damaged` and `lost`.
- **SQL**: `ALTER TABLE borrow_transaction_items MODIFY COLUMN status ENUM('pending', 'borrowed', 'returned', 'damaged', 'lost', 'expired') DEFAULT 'pending';`

---

## Phase 2: Returning Module Enhancements
Modify how books are returned to capture their condition.

### 2.1 Backend Logic (`ReturningRepository` & `ReturningController`)
- Update `markAsReturned` method to accept a `condition` parameter (`good`, `damaged`, `lost`).
- If `damaged` or `lost`:
    - Set `books.availability` to the condition.
    - Set `borrow_transaction_items.status` to the condition.
- If `good`:
    - Set `books.availability` to `available`.
    - Set `borrow_transaction_items.status` to `returned`.

### 2.2 Frontend UI (`returning.js` & View)
- Add a "Condition" dropdown/selection in the Return confirmation modal.
- Options: **Good (Default)**, **Damaged**, **Lost**.

---

## Phase 3: Book Management & Accountability Trace
Allow admins to see the status and who was responsible.

### 3.1 UI Updates (`Book Management`)
- Update table badges to show colors for `Damaged` (Amber) and `Lost` (Red).
- Add a "View Accountability" or "Issue History" button for non-available books.

### 3.2 Traceability Logic
- Create a method to fetch the last borrower of a specific `book_id` where the transaction item status is `damaged` or `lost`.
- **Join**: `borrow_transaction_items` -> `borrow_transactions` -> `users/students/faculty`.

---

## Phase 4: Security & Borrowing Restrictions
Prevent system errors when dealing with damaged/lost inventory.

### 4.1 Cart & Borrowing Validation
- Update `CartRepository` and `BorrowingController` to block adding/checking out books if their availability is NOT `available`.
- Return a clear error message: "This book is currently marked as Damaged/Lost and cannot be borrowed."

---

## Phase 5: Audit Logging
- Ensure all status changes to `damaged` or `lost` are recorded in the `audit_logs` table with details of the Librarian who performed the action and the Borrower involved.
