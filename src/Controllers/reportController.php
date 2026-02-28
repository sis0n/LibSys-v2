<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReportRepository;
use Exception;

class ReportController extends Controller
{
    public function getCirculatedBooksReport()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getCirculatedBooksSummary();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching circulated books report: ' . $e->getMessage()]);
        }
    }

    public function getCirculatedEquipmentsReport()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getCirculatedEquipmentsSummary();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching circulated equipments report: ' . $e->getMessage()]);
        }
    }

    public function getTopVisitors()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getTopVisitorsByYear();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching top visitors report: ' . $e->getMessage()]);
        }
    }

    public function getTopBorrowers()
    {
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getTopBorrowers();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching top borrowers report: ' . $e->getMessage()]);
        }
    }

    public function getMostBorrowedBooks()
    {
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getMostBorrowedBooks();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching most borrowed books report: ' . $e->getMessage()]);
        }
    }

    public function getLibraryVisitsByDepartment()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $data = $repository->getLibraryVisitsByDepartment();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching library visits by department report: ' . $e->getMessage()]);
        }
    }

    public function getDeletedBooks()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $dbData = $repository->getDeletedBooksReport();

            $statsByYear = [];
            foreach ($dbData as $row) {
                $statsByYear[$row['year']] = [
                    'month' => (int)$row['month'],
                    'today' => (int)$row['today'],
                    'count' => (int)$row['count']
                ];
            }

            $years = [2025, 2026, 2027];
            $reportData = [];
            $totalCount = 0;
            $totalMonth = 0;
            $totalToday = 0;

            foreach ($years as $year) {
                $stats = $statsByYear[$year] ?? ['month' => 0, 'today' => 0, 'count' => 0];
                $reportData[] = [
                    "year" => (string)$year,
                    "month" => $stats['month'],
                    "today" => $stats['today'],
                    "count" => $stats['count']
                ];
                $totalCount += $stats['count'];
                $totalMonth += $stats['month'];
                $totalToday += $stats['today'];
            }

            $reportData[] = [
                "year" => "TOTAL",
                "month" => $totalMonth,
                "today" => $totalToday,
                "count" => $totalCount
            ];

            echo json_encode(['success' => true, 'data' => $reportData]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching deleted books report: ' . $e->getMessage()]);
        }
    }

    public function getReportGraphData()
    {
        header('Content-Type: application/json');
        try {
            $repository = new ReportRepository();
            $response = [
                'success' => true,
                'topVisitors' => $repository->getTopVisitors(),
                'weeklyActivity' => $repository->getWeeklyActivity(),
            ];
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load graph data: ' . $e->getMessage(),
            ]);
        }
    }
}