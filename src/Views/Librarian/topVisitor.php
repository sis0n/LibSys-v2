<main class="min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-4">Library Reports</h2>
            <p class="text-gray-700">Comprehensive library statistics and analytics dashboard</p>
        </div>
        <div class="flex gap-2 text-sm">
            <button
                class="inline-flex items-center bg-white font-medium border border-orange-200 justify-center px-4 py-2 rounded-lg hover:bg-gray-100 px-4 gap-2"
                id="download-report-btn">
                <i class="ph ph-download-simple"></i>
                Download Report
            </button>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Top Visitors Chart -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                    <i class="ph ph-chart-bar text-green-500"></i>
                    Top Visitors
                </h3>
                <span class="text-sm bg-orange-100 text-orange-600 font-medium px-3 py-1 rounded-md">This Month</span>
            </div>
            <canvas id="topVisitorsChart"></canvas>
        </div>

        <!-- Weekly Activity Chart -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                    <i class="ph ph-chart-line text-blue-500"></i>
                    Weekly Activity
                </h3>
                <p class="text-sm text-gray-500">Daily visitors and book checkouts</p>
            </div>
            <canvas id="weeklyActivityChart"></canvas>
        </div>
    </div>

   <!-- First Row of Tables -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <!-- Circulated Books Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4 text-center mt-2">Circulated Books</h3>
            <div class="flex-1 overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Category</th>
                            <th scope="col" class="px-4 py-3 text-center">Today</th>
                            <th scope="col" class="px-4 py-3 text-center">Week</th>
                            <th scope="col" class="px-4 py-3 text-center">Month</th>
                            <th scope="col" class="px-4 py-3 text-center">Year</th>
                        </tr>
                    </thead>
                    <tbody id="circulated-books-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- Circulated Equipments Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4 text-center mt-2">Circulated Equipments</h3>
            <div class="flex-1 overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Category</th>
                            <th scope="col" class="px-4 py-3 text-center">Today</th>
                            <th scope="col" class="px-4 py-3 text-center">Week</th>
                            <th scope="col" class="px-4 py-3 text-center">Month</th>
                            <th scope="col" class="px-4 py-3 text-center">Year</th>
                        </tr>
                    </thead>
                    <tbody id="circulated-equipments-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- Deleted Books Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4 text-center mt-2">Deleted Books</h3>
            <div class="flex-1 overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Year</th>
                            <th scope="col" class="px-4 py-3 text-center">Month</th>
                            <th scope="col" class="px-4 py-3 text-center">Today</th>
                            <th scope="col" class="px-4 py-3 text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody id="deleted-books-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Second Row of Tables -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Library Visit (by Department) Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4 flex items-center justify-between">
                Library Visit (by Department)
            </h3>
            <div class="overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Department</th>
                            <th scope="col" class="px-4 py-3 text-center">Today</th>
                            <th scope="col" class="px-4 py-3 text-center">Week</th>
                            <th scope="col" class="px-4 py-3 text-center">Month</th>
                            <th scope="col" class="px-4 py-3 text-center">Year</th>
                        </tr>
                    </thead>
                    <tbody id="library-visit-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- Top 10 Visitors Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4">Top 10 Visitors (by Year)</h3>
            <div class="overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Rank</th>
                            <th scope="col" class="px-4 py-3 text-left">Name</th>
                            <th scope="col" class="px-4 py-3 text-center">Student ID</th>
                            <th scope="col" class="px-4 py-3 text-center">Course</th>
                            <th scope="col" class="px-4 py-3 text-center">Visits</th>
                        </tr>
                    </thead>
                    <tbody id="top-visitors-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Third Row of Tables (New) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Top 10 Borrowers Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4">Top 10 Borrowers (by Year)</h3>
            <div class="overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Rank</th>
                            <th scope="col" class="px-4 py-3 text-left">Name</th>
                            <th scope="col" class="px-4 py-3 text-center">Identifier</th>
                            <th scope="col" class="px-4 py-3 text-center">Role</th>
                            <th scope="col" class="px-4 py-3 text-center">Borrows</th>
                        </tr>
                    </thead>
                    <tbody id="top-borrowers-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- Most Borrowed Books Table -->
        <div class="bg-white border border-orange-200 rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium mb-4">Most Borrowed Books</h3>
            <div class="overflow-x-auto rounded-lg border border-orange-200">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-orange-50 text-gray-700 border-b border-orange-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Rank</th>
                            <th scope="col" class="px-4 py-3 text-left">Book Title</th>
                            <th scope="col" class="px-4 py-3 text-center">Accession</th>
                            <th scope="col" class="px-4 py-3 text-center">Borrows</th>
                        </tr>
                    </thead>
                    <tbody id="most-borrowed-books-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Custom Date Modal -->
    <div id="customDateModal"
        class="fixed inset-0 bg-black-500/20 backdrop-blur-md flex items-center justify-center h-full w-full hidden z-50">
        <div class="relative bg-white rounded-2xl shadow-xl w-[420px] p-6 border border-gray-200">
            <h3 class="text base font-semibold text-gray-800 text-center">
                Pick a date range for the data you want to download.
            </h3>

            <div class="mt-5 space-y-4">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="startDate" name="startDate"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all">
                </div>

                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="endDate" name="endDate"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button id="confirmDateRange"
                    class="px-6 py-2 bg-orange-500 text-white rounded-lg font-medium text-sm shadow-sm hover:bg-orange-600 focus:ring-2 focus:ring-orange-300 transition-all">
                    Confirm
                </button>
                <button id="cancelDateRange"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-medium text-sm hover:bg-gray-200 focus:ring-2 focus:ring-gray-300 transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</main>
<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/js/librarian/reports.js"></script>