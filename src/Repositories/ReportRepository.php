<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Exception;

class ReportRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getCirculatedBooksSummary()
    {
        try {
            $sql = "
                SELECT
                    'Student' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.student_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL
                UNION ALL
                SELECT
                    'Faculty' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.faculty_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL
                UNION ALL
                SELECT
                    'Staff' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.staff_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL
                UNION ALL
                SELECT
                    'TOTAL' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getCirculatedBooksSummary: " . $e->getMessage());
            return [];
        }
    }

    public function getCirculatedEquipmentsSummary()
    {
        try {
            $sql = "
                SELECT
                    'Student' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.student_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL
                UNION ALL
                SELECT
                    'Faculty' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.faculty_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL
                UNION ALL
                SELECT
                    'Staff' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.staff_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL
                UNION ALL
                SELECT
                    'TOTAL' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = CURDATE() THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(CURDATE(), 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) AND MONTH(bt.borrowed_at) = MONTH(CURDATE()) THEN bti.item_id END) AS month,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(CURDATE()) THEN bti.item_id END) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getCirculatedEquipmentsSummary: " . $e->getMessage());
            return [];
        }
    }

    public function getTopVisitorsByYear()
    {
        try {
            $sql = "
                SELECT
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    s.student_number AS student_number,
                    COALESCE(c.course_code, 'N/A') AS course,
                    COUNT(a.user_id) AS visits
                FROM attendance a
                JOIN students s ON a.user_id = s.user_id
                JOIN users u ON s.user_id = u.user_id
                LEFT JOIN courses c ON s.course_id = c.course_id
                WHERE YEAR(a.date) = YEAR(CURDATE())
                GROUP BY a.user_id, u.first_name, u.last_name, s.student_id, c.course_code
                ORDER BY visits DESC
                LIMIT 10;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getTopVisitorsByYear: " . $e->getMessage());
            return [];
        }
    }

    public function getTopBorrowers()
    {
        try {
            $sql = "
                SELECT 
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    u.username AS identifier,
                    u.role,
                    COUNT(bt.transaction_id) AS borrow_count
                FROM borrow_transactions bt
                LEFT JOIN students s ON bt.student_id = s.student_id
                LEFT JOIN faculty f ON bt.faculty_id = f.faculty_id
                LEFT JOIN staff st ON bt.staff_id = st.staff_id
                JOIN users u ON u.user_id = COALESCE(s.user_id, f.user_id, st.user_id)
                WHERE YEAR(bt.borrowed_at) = YEAR(CURDATE())
                GROUP BY u.user_id, u.first_name, u.last_name, u.username, u.role
                ORDER BY borrow_count DESC
                LIMIT 10;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getTopBorrowers: " . $e->getMessage());
            return [];
        }
    }

    public function getMostBorrowedBooks()
    {
        try {
            $sql = "
                SELECT 
                    b.title,
                    b.author,
                    b.accession_number,
                    COUNT(bti.item_id) AS borrow_count
                FROM borrow_transaction_items bti
                JOIN books b ON bti.book_id = b.book_id
                WHERE bti.book_id IS NOT NULL
                GROUP BY b.book_id, b.title, b.author, b.accession_number
                ORDER BY borrow_count DESC
                LIMIT 10;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getMostBorrowedBooks: " . $e->getMessage());
            return [];
        }
    }

    public function getLibraryVisitsByDepartment()
    {
        try {
            $sql = "
                WITH DepartmentVisits AS (
                    SELECT
                        cl.college_name AS department,
                        COUNT(CASE WHEN DATE(a.date) = CURDATE() THEN a.id END) AS today,
                        COUNT(CASE WHEN a.date >= CURDATE() - INTERVAL 6 DAY THEN a.id END) AS week,
                        COUNT(CASE WHEN MONTH(a.date) = MONTH(CURDATE()) AND YEAR(a.date) = YEAR(CURDATE()) THEN a.id END) AS month,
                        COUNT(CASE WHEN YEAR(a.date) = YEAR(CURDATE()) THEN a.id END) AS year
                    FROM colleges cl
                    LEFT JOIN courses c ON cl.college_id = c.college_id
                    LEFT JOIN students s ON c.course_id = s.course_id
                    LEFT JOIN attendance a ON s.user_id = a.user_id
                    GROUP BY cl.college_name
                )
                SELECT * FROM DepartmentVisits
                UNION ALL
                SELECT
                    'TOTAL' AS department,
                    SUM(today) AS today,
                    SUM(week) AS week,
                    SUM(month) AS month,
                    SUM(year) AS year
                FROM DepartmentVisits;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getLibraryVisitsByDepartment: " . $e->getMessage());
            return [];
        }
    }

    public function getDeletedBooksReport()
    {
        try {
            $sql = "
                SELECT
                    YEAR(deleted_at) as year,
                    SUM(CASE WHEN MONTH(deleted_at) = MONTH(CURDATE()) AND YEAR(deleted_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as month,
                    SUM(CASE WHEN DATE(deleted_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                    COUNT(*) as count
                FROM books
                WHERE deleted_at IS NOT NULL
                GROUP BY YEAR(deleted_at)
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getDeletedBooksReport: " . $e->getMessage());
            return [];
        }
    }

    // --- PDF Generation Methods ---

    public function getLibraryResourcesData($startDate, $endDate)
    {
        return [
            [ 'year' => 2025, 'title' => '-', 'volume' => '-', 'processed' => '-' ],
            [ 'year' => 2026, 'title' => '-', 'volume' => '-', 'processed' => '-' ],
            [ 'year' => 2027, 'title' => '-', 'volume' => '-', 'processed' => '-' ],
        ];
    }

    public function getDeletedBooksData($startDate, $endDate)
    {
        try {
            $sql = "
                SELECT
                    SUM(CASE WHEN DATE(deleted_at) = :endDate THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN YEARWEEK(deleted_at, 1) = YEARWEEK(:endDate, 1) THEN 1 ELSE 0 END) as week,
                    SUM(CASE WHEN MONTH(deleted_at) = MONTH(:endDate) AND YEAR(deleted_at) = YEAR(:endDate) THEN 1 ELSE 0 END) as month,
                    COUNT(*) as year
                FROM books
                WHERE deleted_at BETWEEN :startDate AND :endDate;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getDeletedBooksData: " . $e->getMessage());
            return [];
        }
    }

    public function getCirculatedBooksData($startDate, $endDate)
    {
        try {
            $sql = "
                SELECT
                    'Student' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.student_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'Faculty' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.faculty_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'Staff' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.staff_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'TOTAL' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bti.status IN ('borrowed', 'returned', 'overdue') AND bti.book_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getCirculatedBooksData: " . $e->getMessage());
            return [];
        }
    }

    public function getCirculatedEquipmentsData($startDate, $endDate)
    {
        try {
            $sql = "
                SELECT
                    'Student' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.student_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'Faculty' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.faculty_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'Staff' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bt.staff_id IS NOT NULL AND bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate
                UNION ALL
                SELECT
                    'TOTAL' AS category,
                    COUNT(CASE WHEN DATE(bt.borrowed_at) = :endDate THEN bti.item_id END) AS today,
                    COUNT(CASE WHEN YEARWEEK(bt.borrowed_at, 1) = YEARWEEK(:endDate, 1) THEN bti.item_id END) AS week,
                    COUNT(CASE WHEN YEAR(bt.borrowed_at) = YEAR(:endDate) AND MONTH(bt.borrowed_at) = MONTH(:endDate) THEN bti.item_id END) AS month,
                    COUNT(bti.item_id) AS year
                FROM borrow_transactions bt JOIN borrow_transaction_items bti ON bt.transaction_id = bti.transaction_id
                WHERE bti.status IN ('borrowed', 'returned', 'overdue') AND bti.equipment_id IS NOT NULL AND DATE(bt.borrowed_at) BETWEEN :startDate AND :endDate;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getCirculatedEquipmentsData: " . $e->getMessage());
            return [];
        }
    }

    public function getTopVisitorsData($startDate, $endDate)
    {
        try {
            $sql = "
                SELECT
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    s.student_number AS student_number,
                    COALESCE(c.course_code, 'N/A') AS course,
                    COUNT(a.user_id) AS visits
                FROM attendance a
                JOIN students s ON a.user_id = s.user_id
                JOIN users u ON s.user_id = u.user_id
                LEFT JOIN courses c ON s.course_id = c.course_id
                WHERE a.date BETWEEN :startDate AND :endDate
                GROUP BY a.user_id, u.first_name, u.last_name, s.student_id, c.course_code
                ORDER BY visits DESC
                LIMIT 10;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getTopVisitorsData: " . $e->getMessage());
            return [];
        }
    }

    public function getLibraryVisitsData($startDate, $endDate)
    {
        try {
            $sql = "
                WITH DepartmentVisits AS (
                    SELECT
                        cl.college_name AS department,
                        COUNT(CASE WHEN DATE(a.date) = :endDate THEN a.id END) AS today,
                        COUNT(CASE WHEN a.date BETWEEN DATE_SUB(:endDate, INTERVAL 6 DAY) AND :endDate THEN a.id END) AS week,
                        COUNT(CASE WHEN MONTH(a.date) = MONTH(:endDate) AND YEAR(a.date) = YEAR(:endDate) THEN a.id END) AS month,
                        COUNT(a.id) AS year
                    FROM colleges cl
                    LEFT JOIN courses c ON cl.college_id = c.college_id
                    LEFT JOIN students s ON c.course_id = s.course_id
                    LEFT JOIN attendance a ON s.user_id = a.user_id AND a.date BETWEEN :startDate AND :endDate
                    GROUP BY cl.college_name
                )
                SELECT * FROM DepartmentVisits
                UNION ALL
                SELECT
                    'TOTAL' AS department,
                    SUM(today) AS today,
                    SUM(week) AS week,
                    SUM(month) AS month,
                    SUM(year) AS year
                FROM DepartmentVisits;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ReportRepository error in getLibraryVisitsData: " . $e->getMessage());
            return [];
        }
    }

    public function getTopVisitors(int $limit = 5): array
    {
        $sql = "
            SELECT u.first_name, u.last_name, COUNT(a.user_id) AS visits
            FROM attendance a
            JOIN users u ON u.user_id = a.user_id
            WHERE DATE(a.first_scan_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY a.user_id
            ORDER BY visits DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($r) {
        return [
            'user_name' => trim($r['first_name'] . ' ' . $r['last_name']),
            'visits' => (int)$r['visits']
        ];
        }, $rows);
    }

    public function getWeeklyActivity(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day = date('D', strtotime($date));

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT user_id) FROM attendance WHERE DATE(first_scan_at) = :date");
        $stmt->execute(['date' => $date]);
        $visitors = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrow_transactions WHERE DATE(borrowed_at) = :date");
        $stmt->execute(['date' => $date]);
        $borrows = (int) $stmt->fetchColumn();

        $data[] = ['day' => $day, 'visitors' => $visitors, 'borrows' => $borrows];
        }

        return $data;
    }
}