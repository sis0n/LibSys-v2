document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("logTableBody");
  const resultsIndicator = document.getElementById("resultsIndicator");
  const logSearchInput = document.getElementById("logSearchInput");
  const actionFilter = document.getElementById("actionFilter");
  const resourceFilter = document.getElementById("resourceFilter");
  const refreshLogsBtn = document.getElementById("refreshLogsBtn");
  const paginationList = document.getElementById("paginationList");
  const paginationControls = document.getElementById("paginationControls");
  const pageIndicator = document.getElementById("pageIndicator");

  let currentPage = 1;
  let totalPages = 1;
  let debounceTimer;

  async function loadLogs(page = 1) {
    currentPage = page;
    const search = logSearchInput.value.trim();
    const action = actionFilter.value;
    const resource = resourceFilter.value;

    tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <i class="ph ph-spinner animate-spin text-3xl"></i>
                        <span>Loading records...</span>
                    </div>
                </td>
            </tr>
        `;

    try {
      const params = new URLSearchParams({
        page: page,
        search: search,
        action: action,
        resource: resource,
        limit: 50,
      });

      const response = await fetch(
        `${BASE_URL}/api/superadmin/auditLogs/fetch?${params.toString()}`,
      );
      const result = await response.json();

      if (result.success) {
        totalPages = result.totalPages;
        renderLogs(result.logs);
        renderPagination(result.totalPages, result.totalCount);
        resultsIndicator.innerHTML = `Found <span class="text-orange-600 font-bold">${result.totalCount}</span> activity records`;
      } else {
        tableBody.innerHTML = `<tr><td colspan="5" class="py-10 text-center text-red-500">Failed to load logs: ${result.message}</td></tr>`;
      }
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="5" class="py-10 text-center text-red-500">An error occurred while fetching logs.</td></tr>`;
    }
  }

  function renderLogs(logs) {
    if (!logs || logs.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="py-16 text-center text-gray-500 italic">No activity records found matching your filters.</td></tr>`;
      return;
    }

    tableBody.innerHTML = logs
      .map((log) => {
        const date = new Date(log.created_at);
        const formattedDate = date.toLocaleDateString("en-US", {
          month: "short",
          day: "numeric",
          year: "numeric",
        });
        const formattedTime = date.toLocaleTimeString("en-US", {
          hour: "2-digit",
          minute: "2-digit",
          hour12: true,
        });
        const user = log.first_name
          ? `${log.first_name} ${log.last_name}`
          : log.username || "System";
        const role = log.role
          ? `<span class="block text-[10px] text-gray-400 font-bold uppercase">${log.role}</span>`
          : "";
        let actionColor = "text-gray-600 bg-gray-100";
        if (log.action === "ADD") actionColor = "text-green-700 bg-green-100";
        else if (log.action === "UPDATE")
          actionColor = "text-blue-700 bg-blue-100";
        else if (log.action === "DELETE")
          actionColor = "text-red-700 bg-red-100";
        else if (log.action === "LOGIN")
          actionColor = "text-orange-700 bg-orange-100";

        return `
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col leading-tight tabular-nums">
                            <span class="font-bold text-gray-800 text-[12px]">${formattedDate}</span>
                            <span class="text-gray-400 text-[10px] font-bold uppercase">${formattedTime}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col leading-tight">
                            <span class="font-bold text-gray-700 text-[13px]">${user}</span>
                            ${role}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black tracking-tighter uppercase ${actionColor}">
                            ${log.action}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-tight">
                            ${log.resource}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-[12px] text-gray-600 font-medium leading-relaxed max-w-md" title="${log.details}">
                            ${log.details}
                        </p>
                    </td>
                </tr>
            `;
      })
      .join("");
  }

  function renderPagination(total, count) {
    totalPages = total;
    if (!paginationControls || !paginationList || !pageIndicator) return;

    if (totalPages <= 1) {
      paginationControls.classList.add("hidden");
      pageIndicator.classList.add("hidden");
      return;
    }

    paginationControls.className =
      "flex items-center justify-center bg-white border border-gray-200 rounded-full shadow-md px-4 py-2 mt-8 w-fit mx-auto gap-3";
    paginationControls.classList.remove("hidden");
    pageIndicator.classList.remove("hidden");
    pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;

    paginationList.innerHTML = "";

    const createPageLink = (
      type,
      text,
      pageNum,
      isDisabled = false,
      isActive = false,
    ) => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.href = "#";
      a.setAttribute("data-page", String(pageNum));

      let baseClasses = `flex items-center justify-center min-w-[32px] h-9 text-sm font-medium transition-all duration-200 cursor-pointer`;

      if (type === "prev" || type === "next") {
        a.innerHTML = text;
        baseClasses += ` text-gray-700 hover:text-orange-600 px-3`;
        if (isDisabled)
          baseClasses += ` opacity-50 cursor-not-allowed pointer-events-none`;
      } else if (type === "ellipsis") {
        a.textContent = text;
        baseClasses += ` text-gray-400 cursor-default px-2`;
      } else {
        a.textContent = text;
        if (isActive) {
          baseClasses += ` text-white bg-orange-600 rounded-full shadow-sm px-3`;
        } else {
          baseClasses += ` text-gray-700 hover:text-orange-600 hover:bg-orange-100 rounded-full px-3`;
        }
      }

      a.className = baseClasses;
      li.appendChild(a);
      paginationList.appendChild(li);
    };

    createPageLink(
      "prev",
      `<i class="flex ph ph-caret-left text-lg"></i> Previous`,
      currentPage - 1,
      currentPage === 1,
    );

    const windowSize = 1;
    let pagesToShow = new Set([1, totalPages, currentPage]);
    for (let i = 1; i <= windowSize; i++) {
      if (currentPage - i > 0) pagesToShow.add(currentPage - i);
      if (currentPage + i <= totalPages) pagesToShow.add(currentPage + i);
    }

    const sortedPages = [...pagesToShow].sort((a, b) => a - b);
    let lastPage = 0;

    for (const p of sortedPages) {
      if (p > lastPage + 1) createPageLink("ellipsis", "…", "...");
      createPageLink("number", p, p, false, p === currentPage);
      lastPage = p;
    }

    createPageLink(
      "next",
      `Next <i class="flex ph ph-caret-right text-lg"></i>`,
      currentPage + 1,
      currentPage === totalPages,
    );

    paginationList.onclick = (e) => {
      e.preventDefault();
      const target = e.target.closest("a[data-page]");
      if (!target) return;
      const pageStr = target.dataset.page;
      if (pageStr === "...") return;
      const pageNum = parseInt(pageStr, 10);
      if (!isNaN(pageNum) && pageNum !== currentPage) {
        loadLogs(pageNum);
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    };
  }

  logSearchInput.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadLogs(1), 500);
  });

  actionFilter.addEventListener("change", () => loadLogs(1));
  resourceFilter.addEventListener("change", () => loadLogs(1));
  refreshLogsBtn.addEventListener("click", () => loadLogs(1));

  loadLogs(1);
});
