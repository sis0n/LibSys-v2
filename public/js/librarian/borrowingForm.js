document.addEventListener('DOMContentLoaded', () => {

  function showSuccessToast(title, body = "") {
    if (typeof Swal == "undefined") return alert(title);
    Swal.fire({
      toast: true,
      position: "bottom-end",
      showConfirmButton: false,
      timer: 3000,
      width: "360px",
      background: "transparent",
      html: `<div class="flex flex-col text-left"><div class="flex items-center gap-3 mb-2"><div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600"><i class="ph ph-check-circle text-lg"></i></div><div><h3 class="text-[15px] font-semibold text-green-600">${title}</h3><p class="text-[13px] text-gray-700 mt-0.5">${body}</p></div></div></div>`,
      customClass: { popup: "!rounded-xl !shadow-md !border-2 !border-green-400 !p-4 !bg-gradient-to-b !from-[#fffdfb] !to-[#f0fff5] shadow-[0_0_8px_#22c55e70]" },
    });
  }

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
      customClass: { popup: "!rounded-xl !shadow-md !border-2 !border-red-400 !p-4 !bg-gradient-to-b !from-[#fffdfb] !to-[#fff6ef] shadow-[0_0_8px_#ff6b6b70]" },
    });
  }

  function showLoadingModal(message = "Processing request...", subMessage = "Please wait.") {
    if (typeof Swal == "undefined") return;
    Swal.fire({
      background: "transparent",
      html: `<div class="flex flex-col items-center justify-center gap-3"><div class="animate-spin rounded-full h-12 w-12 border-4 border-orange-200 border-t-orange-600"></div><p class="text-gray-700 text-[15px] font-semibold">${message}</p><span class="text-[13px] text-gray-500">${subMessage}</span></div>`,
      allowOutsideClick: false,
      showConfirmButton: false,
      customClass: { popup: "!w-64 !rounded-xl !shadow-md !border-2 !border-orange-400 !p-7 !bg-gradient-to-b !from-[#fffdfb] !to-[#fff6ef] shadow-[0_0_9px_#ffb34770]" },
    });
  }

  async function showConfirmationModal(title, text, confirmText = "Confirm") {
    if (typeof Swal == "undefined") return confirm(title);
    const result = await Swal.fire({
      background: "transparent",
      html: `<div class="flex flex-col text-center"><div class="flex justify-center mb-3"><div class="flex items-center justify-center w-14 h-14 rounded-full bg-orange-100 text-orange-600"><i class="ph ph-warning-circle text-2xl"></i></div></div><h3 class="text-[17px] font-semibold text-orange-700">${title}</h3><p class="text-[14px] text-gray-700 mt-1">${text}</p></div>`,
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: "Cancel",
      customClass: {
        popup: "!rounded-xl !shadow-md !p-6 !bg-gradient-to-b !from-[#fffdfb] !to-[#fff6ef] !border-2 !border-orange-400 shadow-[0_0_8px_#ffb34770]",
        confirmButton: "!bg-orange-600 !text-white !px-5 !py-2.5 !rounded-lg hover:!bg-orange-700",
        cancelButton: "!bg-gray-200 !text-gray-800 !px-5 !py-2.5 !rounded-lg hover:!bg-gray-300",
      },
    });
    return result.isConfirmed;
  }

  const setupDropdown = (btnId, menuId, valueId, inputId, itemClass, callback) => {
    const dropdownBtn = document.getElementById(btnId);
    const dropdownMenu = document.getElementById(menuId);
    const dropdownValue = document.getElementById(valueId);
    const hiddenInput = document.getElementById(inputId);
    if (!dropdownBtn || !dropdownMenu || !dropdownValue || !hiddenInput) return;

    dropdownBtn.addEventListener('click', (e) => {
      e.preventDefault(); e.stopPropagation();
      document.querySelectorAll('.absolute.z-10, .absolute.z-20').forEach(menu => {
        if (menu.id !== menuId) menu.classList.add('hidden');
      });
      dropdownMenu.classList.toggle('hidden');
    });

    dropdownMenu.addEventListener('click', (e) => {
      const target = e.target.closest(`.${itemClass}`);
      if (target) {
        const val = target.dataset.value;
        const name = target.textContent.trim();
        dropdownValue.textContent = name;
        hiddenInput.value = val;
        dropdownMenu.classList.add('hidden');
        if (callback) callback(val);
      }
    });
  };

  const itemIcon = document.getElementById('item_icon');
  const itemNameWrapper = document.getElementById('item_name_wrapper');
  const accessionWrapper = document.getElementById('accession_number_wrapper');
  const bookTitleWrapper = document.getElementById('book_title_wrapper');

  const handleItemTypeChange = (type) => {
    if (!itemIcon) return;
    if (type === 'Book') {
      itemIcon.className = 'ph ph-book-open text-3xl text-emerald-600';
      if (itemNameWrapper) itemNameWrapper.style.display = 'none';
      if (accessionWrapper) accessionWrapper.style.display = 'block';
      if (bookTitleWrapper) bookTitleWrapper.style.display = 'block';
    } else {
      itemIcon.className = 'ph ph-desktop text-3xl text-emerald-600';
      if (itemNameWrapper) itemNameWrapper.style.display = 'block';
      if (accessionWrapper) accessionWrapper.style.display = 'none';
      if (bookTitleWrapper) bookTitleWrapper.style.display = 'none';
    }
  };

  setupDropdown('itemTypeDropdownBtn', 'itemTypeDropdownMenu', 'itemTypeDropdownValue', 'item_type', 'item-type-item', handleItemTypeChange);
  setupDropdown('roleDropdownBtn', 'roleDropdownMenu', 'roleDropdownValue', 'role', 'role-item');

  const populateCollaterals = async () => {
    const list = document.getElementById('collateral-list');
    const hiddenInput = document.getElementById('collateral_id_hidden');
    const valueDisplay = document.getElementById('collateralDropdownValue');
    const menu = document.getElementById('collateralDropdownMenu');
    const btn = document.getElementById('collateralDropdownBtn');

    if (!list || !btn) return;

    btn.addEventListener('click', (e) => {
        e.preventDefault(); e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    try {
        const res = await fetch(`${BASE_URL}/api/librarian/borrowingForm/getCollaterals`);
        const data = await res.json();
        list.innerHTML = "";
        data.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `<button type="button" class="collateral-item w-full text-left px-4 py-2 text-sm hover:bg-amber-50" data-id="${item.collateral_id}">${item.name}</button>`;
            li.addEventListener('click', () => {
                valueDisplay.textContent = item.name;
                hiddenInput.value = item.collateral_id;
                menu.classList.add('hidden');
            });
            list.appendChild(li);
        });
    } catch (err) { console.error("Collateral fetch failed:", err); }
  };

  populateCollaterals();

  document.getElementById('clear-btn').addEventListener('click', () => {
    const form = document.getElementById('main-borrow-form');
    form.reset();
    document.getElementById('roleDropdownValue').textContent = 'Select Role';
    document.getElementById('itemTypeDropdownValue').textContent = 'Equipment';
    document.getElementById('collateralDropdownValue').textContent = 'Select Collateral';
    document.getElementById('role').value = '';
    document.getElementById('item_type').value = 'Equipment';
    document.getElementById('collateral_id_hidden').value = '';
    handleItemTypeChange('Equipment');
    showSuccessToast('Form Cleared', 'Borrower and Item fields have been reset.');
  });

  document.getElementById('check-btn').addEventListener('click', async () => {
    const userId = document.getElementById('input_user_id').value.trim();
    if (!userId) return showErrorToast('Input Required', 'Please enter a **User ID**.');
    showLoadingModal('Checking User...', 'Verifying User ID.');
    try {
      const res = await fetch(`${BASE_URL}/api/librarian/borrowingForm/checkUser`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ input_user_id: userId })
      });
      const data = await res.json();
      Swal.close();
      if (data.exists) {
        if (await showConfirmationModal('User ID Exists', 'Fill the form with existing data?', 'Yes, Fill Fields')) {
          document.querySelector('input[name="first_name"]').value = data.data.first_name;
          document.querySelector('input[name="middle_name"]').value = data.data.middle_name || '';
          document.querySelector('input[name="last_name"]').value = data.data.last_name;
          document.querySelector('input[name="suffix"]').value = data.data.suffix || '';
          document.querySelector('input[name="email"]').value = data.data.email || '';
          document.querySelector('input[name="contact"]').value = data.data.contact || '';
          const displayRole = data.data.role.charAt(0).toUpperCase() + data.data.role.slice(1);
          document.getElementById('roleDropdownValue').textContent = displayRole;
          document.getElementById('role').value = displayRole;
        }
      } else showErrorToast('Not Found', 'User ID not found. Use Guest Mode.');
    } catch { Swal.close(); showErrorToast('Error', 'Unexpected error.'); }
  });

  document.getElementById('main-borrow-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const collateralHidden = document.getElementById('collateral_id_hidden').value;
    if (!collateralHidden) return showErrorToast('Invalid Collateral', 'Mangyaring pumili ng collateral mula sa listahan.');
    
    showLoadingModal('Submitting...', 'Processing transaction.');
    if (!formData.get('role')) formData.set('role', '');
    if (!formData.get('equipment_type')) formData.set('equipment_type', formData.get('item_type'));
    try {
      const res = await fetch(`${BASE_URL}/api/librarian/borrowingForm/create`, { method: 'POST', body: formData });
      const data = await res.json();
      Swal.close();
      if (data.success) { showSuccessToast('Success!', 'Item borrowed.'); form.reset(); }
      else showErrorToast('Failed', data.message || 'Check fields.');
    } catch { Swal.close(); showErrorToast('Error', 'Submission failed.'); }
  });

  const setupCombobox = (inputId, suggestionsId, listId, arrowId, fetchUrl, hiddenInputId) => {
    const input = document.getElementById(inputId);
    const container = document.getElementById(suggestionsId);
    const list = document.getElementById(listId);
    const arrow = document.getElementById(arrowId);
    const hiddenInput = document.getElementById(hiddenInputId);
    if (!input || !container || !list || !arrow || !hiddenInput) return;

    let itemsData = [];

    (async () => {
      try {
        const res = await fetch(`${BASE_URL}/${fetchUrl}`);
        itemsData = await res.json();
      } catch (err) { console.error("Fetch failed:", err); }
    })();

    const render = (filter = "") => {
      list.innerHTML = "";
      const filtered = itemsData.filter(i => (i.equipment_name || "").toLowerCase().includes(filter.toLowerCase()));
      if (filtered.length === 0) { container.classList.add('hidden'); return; }
      filtered.forEach(item => {
        const li = document.createElement('li');
        li.className = "px-4 py-2 text-sm cursor-pointer hover:bg-orange-50 transition-colors";
        const name = item.equipment_name || "";
        const id = item.equipment_id || "";
        li.textContent = name;
        li.addEventListener('click', () => {
          input.value = name;
          hiddenInput.value = id;
          container.classList.add('hidden');
        });
        list.appendChild(li);
      });
      container.classList.remove('hidden');
    };

    input.addEventListener('input', (e) => { hiddenInput.value = ""; render(e.target.value); });
    input.addEventListener('focus', () => render(input.value));
    arrow.addEventListener('click', (e) => { e.stopPropagation(); container.classList.contains('hidden') ? render(input.value) : container.classList.add('hidden'); });
    document.addEventListener('click', (e) => { if (!input.contains(e.target) && !arrow.contains(e.target)) container.classList.add('hidden'); });
  };

  setupCombobox('item_name', 'item_name_suggestions', 'item_name_suggestions_list', 'item_name_dropdown_arrow', 'api/librarian/borrowingForm/getEquipments', 'equipment_id_hidden');
});