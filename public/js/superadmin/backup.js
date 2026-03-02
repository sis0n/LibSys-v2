// 🟠 CONFIRMATION MODAL (ORANGE BORDER)
async function showConfirmationModal(title, text, confirmText = "Confirm") {
    if (typeof Swal == "undefined") return confirm(title);
    const result = await Swal.fire({
        background: "transparent",
        buttonsStyling: false,
        width: '450px',
        html: `
            <div class="flex flex-col text-center">
                <div class="flex justify-center mb-3">
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-600">
                        <i class="ph ph-warning-circle text-3xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">${title}</h3>
                <p class="text-[14px] text-gray-700 mt-1">${text}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: "Cancel",
        customClass: {
            popup: "!rounded-xl !shadow-lg !p-6 !bg-white !border-2 !border-orange-400 shadow-[0_0_15px_#ffb34780]",
            confirmButton: "!bg-orange-600 !text-white !px-5 !py-2.5 !rounded-lg hover:!bg-orange-700 !mx-2 !font-semibold !text-base",
            cancelButton: "!bg-gray-200 !text-gray-800 !px-5 !py-2.5 !rounded-lg hover:!bg-gray-300 !mx-2 !font-semibold !text-base",
            actions: "!mt-4"
        },
    });
    return result.isConfirmed;
}

// 🟢 SUCCESS TOAST
function showSuccessToast(title, body = "Successfully processed.") {
    if (typeof Swal == "undefined") return alert(title);
    Swal.fire({
        toast: true,
        position: "bottom-end",
        showConfirmButton: false,
        timer: 3000,
        width: "360px",
        background: "transparent",
        html: `<div class="flex flex-col text-left"><div class="flex items-center gap-3 mb-2"><div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600"><i class="ph ph-check-circle text-lg"></i></div><div><h3 class="text-[15px] font-semibold text-green-600">${title}</h3><p class="text-[13px] text-gray-700 mt-0.5">${body}</p></div></div></div>`,
        customClass: {
            popup: "!rounded-xl !shadow-md !border-2 !border-green-400 !p-4 !bg-gradient-to-b !from-[#fffdfb] !to-[#f0fff5] shadow-[0_0_8px_#22c55e70]",
        },
    });
}

// 🔴 ERROR TOAST
function showErrorToast(title, body = "An error occurred during processing.") {
    if (typeof Swal == "undefined") return alert(title);
    Swal.fire({
        toast: true,
        position: "bottom-end",
        showConfirmButton: false,
        timer: 4000,
        width: "360px",
        background: "transparent",
        html: `<div class="flex flex-col text-left"><div class="flex items-center gap-3 mb-2"><div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-600"><i class="ph ph-x-circle text-lg"></i></div><div><h3 class="text-[15px] font-semibold text-red-600">${title}</h3><p class="text-[13px] text-gray-700 mt-0.5">${body}</p></div></div></div>`,
        customClass: {
            popup: "!rounded-xl !shadow-md !border-2 !border-red-400 !p-4 !bg-gradient-to-b !from-[#fffdfb] !to-[#fff6ef] shadow-[0_0_8px_#ff6b6b70]",
        },
    });
}

// 🟠 LOADING MODAL
function showLoadingModal(message = "Processing request...", subMessage = "Please wait.") {
    if (typeof Swal == "undefined") return;
    Swal.fire({
        background: "transparent",
        html: `
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-orange-200 border-t-orange-600"></div>
                <p class="text-gray-700 text-[14px]">${message}<br><span class="text-sm text-gray-500">${subMessage}</span></p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        customClass: {
            popup: "!rounded-xl !shadow-md !border-2 !border-orange-400 !p-6 !bg-gradient-to-b !from-[#fffdfb] !to-[#fff6ef] shadow-[0_0_8px_#ffb34770]",
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const backupFilesTableBody = document.getElementById('backupFilesTableBody');
    const backupFileRowTemplate = document.getElementById('backup-file-row-template')?.content;
    const backupFilesCount = document.getElementById('backupFilesCount');
    const noBackupFilesFound = document.getElementById('noBackupFilesFound');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationNumbersDiv = document.getElementById('pagination-numbers');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const backupButtons = document.querySelectorAll('.backup-btn');
    
    // Upload & Restore elements
    const uploadRestoreForm = document.getElementById('uploadRestoreForm');
    const restoreFile = document.getElementById('restoreFile');
    const fileInfo = document.getElementById('fileInfo');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadRestoreBtn = document.getElementById('uploadRestoreBtn');
    const removeFileBtn = document.getElementById('removeFileBtn');

    if (!backupFilesTableBody || !backupFileRowTemplate) {
        console.error("Required table elements not found.");
        return;
    }

    let allBackupFiles = [];
    const itemsPerPage = 10;
    let currentPage = 1;

    // ====== HELPERS ======
    const showLoadingState = () => {
        backupFilesTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-500">Loading...</td></tr>';
    };

    const clearSelectedFile = () => {
        if (restoreFile) restoreFile.value = "";
        fileInfo?.classList.add('hidden');
        uploadRestoreBtn.disabled = true;
    };

    const renderBackupFiles = (files) => {
        backupFilesTableBody.innerHTML = '';
        backupFilesCount.textContent = allBackupFiles.length;

        if (files.length === 0) {
            noBackupFilesFound.classList.remove('hidden');
            paginationContainer.classList.add('hidden');
            return;
        }
        noBackupFilesFound.classList.add('hidden');

        files.forEach(file => {
            const newRow = backupFileRowTemplate.cloneNode(true);
            newRow.querySelector('.file-name').textContent = file.fileName;
            newRow.querySelector('.created-date').textContent = file.createdDate;
            newRow.querySelector('.created-by').textContent = file.createdByName || 'Unknown User';

            const typeBadge = newRow.querySelector('.file-type-badge');
            typeBadge.textContent = file.type;

            const badgeColors = {
                'SQL.GZ': ['bg-purple-100', 'text-purple-800'],
                'ZIP': ['bg-blue-100', 'text-blue-800'],
                'SQL': ['bg-amber-100', 'text-amber-800'],
                Default: ['bg-gray-100', 'text-gray-800']
            };

            const color = badgeColors[file.type] || badgeColors.Default;
            typeBadge.classList.add(...color);

            // Restore
            const restoreBtn = newRow.querySelector('.restore-btn');
            if (file.type === 'SQL' || file.type === 'SQL.GZ') {
                restoreBtn.onclick = () => confirmRestoreLocal(file.fileName);
            } else {
                restoreBtn.classList.add('hidden'); // Hide restore for ZIP/CSV individual exports
            }

            // Download
            newRow.querySelector('.download-btn').onclick = (e) => secureDownload(file.downloadLink, file.fileName);
            
            // Delete
            newRow.querySelector('.delete-btn').onclick = () => confirmDeleteBackup(file.fileName);

            backupFilesTableBody.appendChild(newRow);
        });
    };

    // --- Actions ---

    const confirmDeleteBackup = async (filename) => {
        const confirmed = await showConfirmationModal(
            "Delete Backup?",
            `Are you sure you want to permanently delete **${filename}**? This cannot be undone.`,
            "Yes, Delete"
        );
        if (!confirmed) return;

        showLoadingModal("Deleting...", "Removing backup file.");
        try {
            const res = await fetch(`${BASE_URL_JS}/api/superadmin/backup/delete/${encodeURIComponent(filename)}`, { method: 'POST' });
            const data = await res.json();
            Swal.close();
            if (data.success) {
                showSuccessToast("Deleted", data.message);
                fetchBackupFiles();
            } else {
                showErrorToast("Error", data.message);
            }
        } catch (err) {
            Swal.close();
            showErrorToast("Network Error", "Failed to delete file.");
        }
    };

    const confirmRestoreLocal = async (filename) => {
        const confirmed = await showConfirmationModal(
            "Restore Database?",
            `This will **OVERWRITE** your entire database using the backup from **${filename}**. This action is destructive. Continue?`,
            "Yes, Restore Now"
        );
        if (!confirmed) return;

        showLoadingModal("Restoring Database...", "Please wait, this may take a minute. Do not close this tab.");
        try {
            const res = await fetch(`${BASE_URL_JS}/api/superadmin/backup/restore/${encodeURIComponent(filename)}`, { method: 'POST' });
            const data = await res.json();
            Swal.close();
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Restoration Successful!',
                    text: 'The database has been restored. The page will now reload.',
                    confirmButtonColor: '#ea580c'
                });
                window.location.reload();
            } else {
                showErrorToast("Restore Failed", data.message);
            }
        } catch (err) {
            Swal.close();
            showErrorToast("Critical Error", "The restore process encountered a network error.");
        }
    };

    // --- Upload & Restore ---
    restoreFile?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            fileNameDisplay.textContent = file.name;
            fileInfo.classList.remove('hidden');
            uploadRestoreBtn.disabled = false;
        } else {
            fileInfo.classList.add('hidden');
            uploadRestoreBtn.disabled = true;
        }
    });

    uploadRestoreForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const file = restoreFile.files[0];
        if (!file) return;

        const confirmed = await showConfirmationModal(
            "Upload & Restore?",
            `You are about to upload **${file.name}** and restore it. This will **WIPE** all current data. Proceed?`,
            "Upload & Restore"
        );
        if (!confirmed) return;

        showLoadingModal("Uploading & Restoring...", "Processing file. This may take a while...");
        
        const formData = new FormData();
        formData.append('backup_file', file);

        try {
            const res = await fetch(`${BASE_URL_JS}/api/superadmin/backup/upload_restore`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            Swal.close();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'System Restored!',
                    text: data.message,
                    confirmButtonColor: '#ea580c'
                });
                window.location.reload();
            } else {
                showErrorToast("Upload Error", data.message);
            }
        } catch (err) {
            Swal.close();
            showErrorToast("Network Error", "Failed to upload or process backup file.");
        }
    });

    const renderPagination = (totalItems, itemsPerPage, currentPage) => {
        paginationNumbersDiv.innerHTML = '';
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        if (totalItems <= itemsPerPage || totalPages <= 1) {
            paginationContainer.classList.add('hidden');
            return;
        }
        paginationContainer.classList.remove('hidden');

        prevPageBtn.classList.toggle('opacity-50', currentPage === 1);
        prevPageBtn.classList.toggle('cursor-not-allowed', currentPage === 1);
        nextPageBtn.classList.toggle('opacity-50', currentPage === totalPages);
        nextPageBtn.classList.toggle('cursor-not-allowed', currentPage === totalPages);

        const createPageBtn = (num, active = false) => {
            const btn = document.createElement('button');
            btn.className = `w-8 h-8 rounded-full text-sm font-medium transition-colors ${active ? 'bg-orange-600 text-white' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600'}`;
            btn.textContent = num;
            btn.onclick = () => goToPage(num);
            paginationNumbersDiv.appendChild(btn);
        };

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                createPageBtn(i, i === currentPage);
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                const dot = document.createElement('span');
                dot.textContent = '...';
                paginationNumbersDiv.appendChild(dot);
            }
        }
    };

    const goToPage = (page) => {
        currentPage = page;
        const start = (currentPage - 1) * itemsPerPage;
        renderBackupFiles(allBackupFiles.slice(start, start + itemsPerPage));
        renderPagination(allBackupFiles.length, itemsPerPage, currentPage);
    };

    const fetchBackupFiles = async () => {
        showLoadingState();
        try {
            const res = await fetch(`${BASE_URL_JS}/api/superadmin/backup/logs`);
            const data = await res.json();
            if (data.success) {
                allBackupFiles = data.logs.map(log => ({
                    fileName: log.file_name,
                    type: log.file_type,
                    size: log.size || 'N/A',
                    createdDate: log.created_at,
                    createdByName: log.created_by_name,
                    downloadLink: `${BASE_URL_JS}/api/superadmin/backup/secure_download/${encodeURIComponent(log.file_name)}`
                }));
                goToPage(1);
            }
        } catch (err) {
            console.error(err);
        }
    };

    const secureDownload = async (url, filename) => {
        showLoadingModal("Downloading...", filename);
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            Swal.close();
        } catch (err) {
            Swal.close();
            showErrorToast("Download Failed", "Failed to retrieve file.");
        }
    };

    backupButtons.forEach(btn => {
        btn.onclick = async () => {
            const type = btn.dataset.type;
            const url = type === 'full_sql' 
                ? `${BASE_URL_JS}/api/superadmin/backup/database/full`
                : `${BASE_URL_JS}/api/superadmin/backup/export/zip/${type}`;
            
            showLoadingModal("Generating Backup...", "This may take a moment.");
            try {
                const res = await fetch(url);
                const data = await res.json();
                Swal.close();
                if (data.success) {
                    showSuccessToast("Success", "Backup generated successfully!");
                    fetchBackupFiles();
                    secureDownload(`${BASE_URL_JS}/api/superadmin/backup/secure_download/${encodeURIComponent(data.filename)}`, data.filename);
                } else {
                    showErrorToast("Backup Failed", data.message);
                }
            } catch (err) {
                Swal.close();
                showErrorToast("Error", "Network error while generating backup.");
            }
        };
    });

    prevPageBtn.onclick = () => currentPage > 1 && goToPage(currentPage - 1);
    nextPageBtn.onclick = () => currentPage < Math.ceil(allBackupFiles.length / itemsPerPage) && goToPage(currentPage + 1);

    fetchBackupFiles();
});