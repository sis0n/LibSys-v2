<?php

namespace App\Controllers;

use App\Core\Controller;

class SidebarController extends Controller
{

    // student Display Caller 

    public function studentDashboard()
    {
        $this->view("student/dashboard", [
            "title" => "Student Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function studentBookCatalog()
    {
        $this->view("student/bookCatalog", [
            "title" => "Book Catalog",
            "currentPage" => "bookCatalog"
        ]);
    }

    public function studentEquipmentCatalog()
    {
        $this->view("student/equipmentCatalog", [
            "title" => "Equipment Catalog",
            "currentPage" => "equipmentCatalog"
        ]);
    }

    public function studentMyCart()
    {
        $this->view("student/myCart", [
            "title" => "My Cart",
            "currentPage" => "myCart"
        ]);
    }

    public function studentQrBorrowingTicket()
    {
        $this->view("student/qrBorrowingTicket", [
            "title" => "QR Borrowing Ticket",
            "currentPage" => "qrBorrowingTicket"
        ]);
    }

    public function studentMyAttendance()
    {
        $this->view("student/myAttendance", [
            "title" => "My Attendance",
            "currentPage" => "myAttendance"
        ]);
    }

    public function studentBorrowingHistory()
    {
        $this->view("student/borrowingHistory", [
            "title" => "Borrowing History",
            "currentPage" => "borrowingHistory"
        ]);
    }

    public function studentChangePassword()
    {
        $this->view("student/changePassword", [
            "title" => "Change Password",
            "currentPage" => "changePassword"
        ]);
    }
    public function studentMyProfile()
    {
        $this->view("student/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }

    // faculty Display Caller

    public function facultyDashboard()
    {
        $this->view("faculty/dashboard", [
            "title" => "faculty Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function facultyBookCatalog()
    {
        $this->view("faculty/bookCatalog", [
            "title" => "Book Catalog",
            "currentPage" => "bookCatalog"
        ]);
    }

    public function facultyEquipmentCatalog()
    {
        $this->view("faculty/equipmentCatalog", [
            "title" => "Equipment Catalog",
            "currentPage" => "equipmentCatalog"
        ]);
    }

    public function facultyMyCart()
    {
        $this->view("faculty/myCart", [
            "title" => "My Cart",
            "currentPage" => "myCart"
        ]);
    }

    public function facultyQrBorrowingTicket()
    {
        $this->view("faculty/qrBorrowingTicket", [
            "title" => "QR Borrowing Ticket",
            "currentPage" => "qrBorrowingTicket"
        ]);
    }

    public function facultyBorrowingHistory()
    {
        $this->view("faculty/borrowingHistory", [
            "title" => "Borrowing History",
            "currentPage" => "borrowingHistory"
        ]);
    }

    public function facultyChangePassword()
    {
        $this->view("faculty/changePassword", [
            "title" => "Change Password",
            "currentPage" => "changePassword"
        ]);
    }
    public function facultyMyProfile()
    {
        $this->view("faculty/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }

    //Staff Display Caller

    public function staffDashboard()
    {
        $this->view("staff/dashboard", [
            "title" => "Staff Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function staffBookCatalog()
    {
        $this->view("staff/bookCatalog", [
            "title" => "Book Catalog",
            "currentPage" => "bookCatalog"
        ]);
    }

    public function staffEquipmentCatalog()
    {
        $this->view("staff/equipmentCatalog", [
            "title" => "Equipment Catalog",
            "currentPage" => "equipmentCatalog"
        ]);
    }

    public function staffMyCart()
    {
        $this->view("staff/myCart", [
            "title" => "My Cart",
            "currentPage" => "myCart"
        ]);
    }

    public function staffQrBorrowingTicket()
    {
        $this->view("staff/qrBorrowingTicket", [
            "title" => "QR Borrowing Ticket",
            "currentPage" => "qrBorrowingTicket"
        ]);
    }

    public function staffBorrowingHistory()
    {
        $this->view("staff/borrowingHistory", [
            "title" => "Borrowing History",
            "currentPage" => "borrowingHistory"
        ]);
    }

    public function staffChangePassword()
    {
        $this->view("staff/changePassword", [
            "title" => "Change Password",
            "currentPage" => "changePassword"
        ]);
    }

    public function staffMyProfile()
    {
        $this->view("staff/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }




    // Super Admin Display Caller 

    public function superAdminDashboard()
    {
        $this->view("superadmin/dashboard", [
            "title" => "Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function userManagement()
    {
        $this->view("superadmin/userManagement", [
            "title" => "User Management",
            "currentPage" => "userManagement"
        ]);
    }

    public function bookManagement()
    {
        $this->view("superadmin/bookManagement", [
            "title" => "Book Management",
            "currentPage" => "bookManagement"
        ]);
    }

    public function equipmentManagement()
    {
        $this->view("superadmin/equipmentManagement", [
            "title" => "Equipment Management",
            "currentPage" => "equipmentManagement"
        ]);
    }

    public function qrScanner()
    {
        $this->view("superadmin/qrScanner", [
            "title" => "QR Code Scanner",
            "currentPage" => "qrScanner"
        ]);
    }

    public function attendanceLogs()
    {
        $this->view("superadmin/attendanceLogs", [
            "title" => "Attendance Logs",
            "currentPage" => "attendanceLogs"
        ]);
    }
    public function topVisitor()
    {
        $this->view("superadmin/topVisitor", [
            "title" => "Reports",
            "currentPage" => "topVisitor"
        ]);
    }

    public function borrowingHistory()
    {
        $this->view("superadmin/transactionHistory", [
            "title" => "Transaction History",
            "currentPage" => "transactionHistory"
        ]);
    }

    public function returning()
    {
        $this->view("superadmin/returning", [
            "title" => "Returning",
            "currentPage" => "returning"
        ]);
    }

    public function borrowingForm()
    {
        $this->view("superadmin/borrowingForm", [
            "title" => "Borrowing Form",
            "currentPage" => "borrowingForm"
        ]);
    }

    public function backup()
    {
        $this->view("superadmin/backup", [
            "title" => "Backup",
            "currentPage" => "backup"
        ]);
    }

    public function globalLogs()
    {
        $this->view("superadmin/globalLogs", [
            "title" => "Global Logs",
            "currentPage" => "globalLogs"
        ]);
    }

    public function AdminBackup()
    {
        $this->view("admin/backup", [
            "title" => "Backup",
            "currentPage" => "backup"
        ]);
    }

    public function restoreBooks()
    {
        $this->view("superadmin/restoreBooks", [
            "title" => "Restore Books",
            "currentPage" => "restoreBooks",
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    }

    public function restoreEquipment()
    {
        $this->view("superadmin/restoreEquipment", [
            "title" => "Restore Equipment",
            "currentPage" => "restoreEquipment"
        ]);
    }

    public function restoreUser()
    {
        $this->view("superadmin/restoreUser", [
            "title" => "Restore User",
            "currentPage" => "restoreUser"
        ]);
    }

    public function changePassword()
    {
        $this->view("superadmin/changePassword", [
            "title" => "Change Password",
            "currentPage" => "changePassword"
        ]);
    }
    public function superadminMyProfile()
    {
        $this->view("superadmin/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }

    // Admin Display Caller

    public function adminDashboard()
    {
        $this->view("admin/dashboard", [
            "title" => "Admin Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function adminBookManagement()
    {
        $this->view("admin/bookManagement", [
            "title" => "Admin Book Management",
            "currentPage" => "bookManagement"
        ]);
    }

    public function adminEquipmentManagement()
    {
        $this->view("admin/equipmentManagement", [
            "title" => "Admin Equipment Management",
            "currentPage" => "equipmentManagement"
        ]);
    }
    public function adminQrScanner()
    {
        $this->view("admin/qrScanner", [
            "title" => "Admin QR Code Scanner",
            "currentPage" => "qrScanner"
        ]);
    }

    public function adminAttendanceLogs()
    {
        $this->view("admin/attendanceLogs", [
            "title" => "Admin Attendance Logs",
            "currentPage" => "attendanceLogs"
        ]);
    }
    public function adminTopVisitor()
    {
        $this->view("admin/topVisitor", [
            "title" => "Admin Reports",
            "currentPage" => "topVisitor"
        ]);
    }

    public function adminBorrowingHistory()
    {
        $this->view("admin/transactionHistory", [
            "title" => "Admin Transaction History",
            "currentPage" => "transactionHistory"
        ]);
    }

    public function adminReturning()
    {
        $this->view("admin/returning", [
            "title" => "Admin Returning",
            "currentPage" => "returning"
        ]);
    }

    public function adminBorrowingForm()
    {
        $this->view("admin/borrowingForm", [
            "title" => "Admin Borrowing Form",
            "currentPage" => "borrowingForm"
        ]);
    }

    public function adminGlobalLogs()
    {
        $this->view("admin/globalLogs", [
            "title" => "Admin Global Logs",
            "currentPage" => "globalLogs"
        ]);
    }


    public function AdminRestoreBooks()
    {
        $this->view("admin/restoreBooks", [
            "title" => "Restore Books",
            "currentPage" => "restoreBooks",
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    }

    public function adminChangePassword()
    {
        $this->view("admin/changePassword", [
            "title" => "Admin Change Password",
            "currentPage" => "changePassword"
        ]);
    }

    public function adminMyProfile()
    {
        $this->view("admin/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }

    // Librarian Display Caller

    public function librarianDashboard()
    {
        $this->view("librarian/dashboard", [
            "title" => "Librarian Dashboard",
            "currentPage" => "dashboard"
        ]);
    }

    public function librarianBookManagement()
    {
        $this->view("librarian/bookManagement", [
            "title" => "Librarian Book Management",
            "currentPage" => "bookManagement"
        ]);
    }

    public function librarianEquipmentManagement()
    {
        $this->view("librarian/equipmentManagement", [
            "title" => "Librarian Equipment Management",
            "currentPage" => "equipmentManagement"
        ]);
    }

    public function librarianQrScanner()
    {
        $this->view(
            "librarian/qrScanner",
            [
                "title" => "Librarian QR Code Scanner",
                "currentPage" => "qrScanner"
            ]
        );
    }

    public function librarianAttendanceLogs()
    {
        $this->view("librarian/attendanceLogs", [
            "title" => "Librarian Attendance Logs",
            "currentPage" => "attendanceLogs"
        ]);
    }
    public function librarianTopVisitor()
    {
        $this->view("librarian/topVisitor", [
            "title" => "Librarian Reports",
            "currentPage" => "topVisitor"
        ]);
    }

    public function librarianBorrowingHistory()
    {
        $this->view("librarian/transactionHistory", [
            "title" => "Librarian Transaction History",
            "currentPage" => "transactionHistory"
        ]);
    }

    public function librarianReturning()
    {
        $this->view("librarian/returning", [
            "title" => "Librarian Returning",
            "currentPage" => "returning"
        ]);
    }

    public function librarianBorrowingForm()
    {
        $this->view("librarian/borrowingForm", [
            "title" => "Librarian Borrowing Form",
            "currentPage" => "borrowingForm"
        ]);
    }

    public function librarianGlobalLogs()
    {
        $this->view("librarian/globalLogs", [
            "title" => "Librarian Global Logs",
            "currentPage" => "globalLogs"
        ]);
    }


    public function LibrarianRestoreBooks()
    {
        $this->view("librarian/restoreBooks", [
            "title" => "Restore Books",
            "currentPage" => "restoreBooks",
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    }

    public function librarianChangePassword()
    {
        $this->view("librarian/changePassword", [
            "title" => "Librarian Change Password",
            "currentPage" => "changePassword"
        ]);
    }

    public function librarianMyProfile()
    {
        $this->view("librarian/myProfile", [
            "title" => "My Profile",
            "currentPage" => "myProfile"
        ]);
    }
}
