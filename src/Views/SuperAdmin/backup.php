<?php if (!empty($csrf_token)) : ?>
<input type="hidden" id="csrf_token" value="<?= $csrf_token ?>">
<?php endif; ?>
<main class="min-h-screen">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-3">
        <div>
            <h2 class="text-2xl font-bold mb-4">Backup & Restore</h2>
            <p class="text-gray-500">Manage and secure your system data backups here.</p>
        </div>
    </div>

    <!-- Create New Backup Section -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="ph ph-folder-plus text-xl text-orange-700"></i>
            <h3 class="text-lg font-semibold text-orange-700">Backup & Data Export</h3>
        </div>
        <p class="text-gray-600 mb-6 text-sm">Create a full system snapshot or export specific table data for reporting and security.</p>

        <div class="space-y-6">
            <!-- Full System Backup -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">System Snapshot</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button" data-type="full_sql" class="backup-btn group flex items-center gap-4 p-4 border border-orange-200 rounded-xl bg-orange-50 hover:bg-orange-600 hover:border-orange-600 transition-all duration-200 text-left">
                        <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-500 transition-colors">
                            <i class="ph ph-database text-2xl text-orange-600 group-hover:text-white"></i>
                        </div>
                        <div>
                            <span class="block text-base font-bold text-gray-800 group-hover:text-white">Full Database Backup</span>
                            <span class="text-xs text-orange-700 group-hover:text-orange-100 italic font-medium tracking-tight">Safest Option • Compressed GZIP</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Specific Table Exports -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Individual Table Export (SQL + CSV)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Transactions Group -->
                    <button type="button" data-type="borrow_transactions" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-swap text-2xl text-blue-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Transactions</span>
                        <span class="text-[10px] text-gray-500 uppercase">Borrowing Data</span>
                    </button>

                    <!-- People Group -->
                    <button type="button" data-type="users" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-users-three text-2xl text-orange-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">User Accounts</span>
                        <span class="text-[10px] text-gray-500 uppercase">All Staff & Admin</span>
                    </button>

                    <!-- Activity Group -->
                    <button type="button" data-type="attendance_logs" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-fingerprint text-2xl text-green-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Attendance</span>
                        <span class="text-[10px] text-gray-500 uppercase">Daily Logs</span>
                    </button>

                    <!-- Security Group -->
                    <button type="button" data-type="audit_logs" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-shield-check text-2xl text-purple-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Audit Logs</span>
                        <span class="text-[10px] text-gray-500 uppercase">System Security</span>
                    </button>

                    <!-- Inventory Group -->
                    <button type="button" data-type="books" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-books text-2xl text-amber-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Book Catalog</span>
                        <span class="text-[10px] text-gray-500 uppercase">Library Books</span>
                    </button>

                    <!-- Inventory Group -->
                    <button type="button" data-type="equipments" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-projector-screen text-2xl text-indigo-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Equipments</span>
                        <span class="text-[10px] text-gray-500 uppercase">Hardware Inventory</span>
                    </button>

                    <!-- Students Group -->
                    <button type="button" data-type="students" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-student text-2xl text-rose-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Student Data</span>
                        <span class="text-[10px] text-gray-500 uppercase">Profile & Records</span>
                    </button>

                    <!-- Extra Group -->
                    <button type="button" data-type="library_policies" class="backup-btn group flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                        <i class="ph ph-scroll text-2xl text-teal-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-semibold text-gray-700 mt-2">Policies</span>
                        <span class="text-[10px] text-gray-500 uppercase">Rules & Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload & Restore Section -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6 mb-6 border-l-4 border-l-orange-500">
        <div class="flex items-center gap-2 mb-4">
            <i class="ph ph-upload-simple text-xl text-orange-700"></i>
            <h3 class="text-lg font-semibold text-orange-700">Upload & Restore Database</h3>
        </div>
        <p class="text-gray-600 mb-6 text-sm">Have a backup file on your computer? Upload it here to restore the entire system. <span class="text-red-500 font-bold">Warning: This will overwrite your current data.</span></p>

        <form id="uploadRestoreForm" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="w-full sm:w-auto flex-1">
                <label class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="ph ph-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-gray-400">SQL or SQL.GZ files only</p>
                    </div>
                    <input type="file" id="restoreFile" name="backup_file" class="hidden" accept=".sql,.gz" />
                </label>
                <div id="fileInfo" class="mt-2 text-sm text-orange-600 font-medium hidden flex items-center justify-between gap-1 bg-orange-50 p-2 rounded-md border border-orange-200">
                    <div class="flex items-center gap-1">
                        <i class="ph ph-file-sql"></i>
                        <span id="fileNameDisplay"></span>
                    </div>
                    <button type="button" id="removeFileBtn" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Remove file">
                        <i class="ph ph-x-circle text-xl"></i>
                    </button>
                </div>
            </div>
            <button type="submit" id="uploadRestoreBtn" class="w-full sm:w-auto px-6 py-3 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2" disabled>
                <i class="ph ph-clock-counter-clockwise text-xl"></i>
                Restore from File
            </button>
        </form>
    </div>

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="ph ph-folder-open text-xl text-orange-700"></i>
            <h3 class="text-lg font-semibold text-orange-700">Backup Files (<span id="backupFilesCount">0</span>)</h3>
        </div>
        <p class="text-gray-600 mb-6">Available backup files for download or deletion.</p>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[40%]">
                            File Name
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                            Type
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">
                            Created Date
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                            Created By
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="backupFilesTableBody" class="bg-white divide-y divide-gray-200"></tbody>
            </table>
            <div id="noBackupFilesFound" class="hidden">
                <div class="flex flex-col items-center justify-center text-gray-500 py-10">
                    <i class="ph ph-folder-simple-x text-4xl block mb-2 text-gray-400"></i>
                    <p>No backup files found.</p>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination-container" class="flex justify-center items-center mt-6 hidden">
            <nav class="bg-white px-6 py-2 rounded-full shadow-sm border border-gray-200">
                <ul class="flex items-center gap-2 text-sm">
                    <li>
                        <a href="#" id="prev-page"
                            class="flex items-center gap-1 text-gray-500 hover:text-gray-800 transition p-2">
                            <i class="ph ph-caret-left"></i>
                            <span>Previous</span>
                        </a>
                    </li>
                    <div id="pagination-numbers" class="flex items-center gap-1">
                    </div>
                    <li>
                        <a href="#" id="next-page"
                            class="flex items-center gap-1 text-gray-500 hover:text-gray-800 transition p-2">
                            <span>Next</span>
                            <i class="ph ph-caret-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</main>

<script>
    // Ensure BASE_URL_JS is defined without causing a redeclaration error
    if (typeof BASE_URL_JS === 'undefined') {
        var BASE_URL_JS = "<?= BASE_URL ?>";
    }
</script>
<script src="<?= BASE_URL ?>/js/superadmin/backup.js"></script>

<template id="backup-file-row-template">
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 align-center">
            <div class="flex items-center gap-2">
                <i class="ph ph-file-zip text-lg text-gray-500"></i>
                <span class="file-name font-medium text-gray-800 break-all"></span>
            </div>
        </td>
        <td class="px-4 py-3 align-center">
            <span class="file-type-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full"></span>
        </td>
        <td class="px-4 py-3 align-center text-gray-600 created-date"></td>
        <td class="px-4 py-3 align-center text-gray-600 created-by"></td>
        <td class="px-4 py-3 align-center text-center">
            <div class="inline-flex items-center justify-center gap-2">
                <button
                    class="restore-btn inline-flex items-center gap-2 px-3 py-1.5 border border-orange-600 rounded-md shadow-sm text-sm font-medium text-orange-600 bg-white hover:bg-orange-50 transition"
                    title="Restore Database">
                    <i class="ph ph-clock-counter-clockwise text-xl"></i>
                    Restore
                </button>
                <button
                    class="download-btn inline-flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition"
                    title="Download File">
                    <i class="ph ph-download-simple text-xl"></i>
                    Download
                </button>
                <button
                    class="delete-btn inline-flex items-center gap-2 px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition"
                    title="Delete File">
                    <i class="ph ph-trash text-xl"></i>
                    Delete
                </button>
            </div>
        </td>
    </tr>
</template>