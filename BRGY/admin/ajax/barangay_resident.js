// Title case conversion
function toTitleCase(str) {
  return str.replace(/\b\w+/g, function (txt) {
    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
  });
}

document.querySelectorAll('input[type="text"]').forEach(input => {
  input.addEventListener('input', function () {
    const caretPos = this.selectionStart;
    const formatted = toTitleCase(this.value);
    this.value = formatted;
    this.setSelectionRange(caretPos, caretPos);
  });
});

$(document).ready(function () {
  $('#notifBtn').click(function () {
    $('.badge-number').fadeOut();
    alert('Open notifications panel...');
  });

  // View button click
  $('.btn.view').on('click', function () {
    const id = $(this).data('id');

    $.ajax({
      url: '../database/resident_view.php',
      method: 'GET',
      data: { id },
      dataType: 'json',
      success: function (res) {
        if (res.error) {
          alert(res.error);
          return;
        }

        $('#view_id').val(res.id);
        $('#view_first_name').val(res.first_name);
        $('#view_middle_name').val(res.middle_name);
        $('#view_last_name').val(res.last_name);
        $('#view_gender').val(res.gender);
        $('#view_date_of_birth').val(res.date_of_birth);
        $('#view_pob_country').val(res.pob_country);
        $('#view_pob_province').val(res.pob_province);
        $('#view_pob_city').val(res.pob_city);
        $('#view_pob_barangay').val(res.pob_barangay);
        $('#view_civil_status').val(res.civil_status);
        $('#view_nationality').val(res.nationality);
        $('#view_religion').val(res.religion);
        $('#view_country').val(res.country);
        $('#view_province').val(res.province);
        $('#view_city').val(res.city);
        $('#view_barangay').val(res.barangay);
        $('#view_zipcode').val(res.zipcode);
        $('#view_house_number').val(res.house_number);
        $('#view_zone_purok').val(res.zone_purok);
        $('#view_residency_date').val(res.residency_date);
        $('#view_years_residency').val(res.years_of_residency);
        $('#view_residency_type').val(res.residency_type);
        $('#view_previous_address').val(res.previous_address);
        $('#view_father_name').val(res.father_name);
        $('#view_mother_name').val(res.mother_name);
        $('#view_spouse_name').val(res.spouse_name);
        $('#view_family_members').val(res.number_of_family_members);
        $('#view_household_number').val(res.household_number);
        $('#view_relationship_head').val(res.relationship_to_head);
        $('#view_house_position').val(res.house_position);
        $('#view_education').val(res.educational_attainment);
        $('#view_current_school').val(res.current_school);
        $('#view_occupation').val(res.occupation);
        $('#view_income').val(res.monthly_income);
        $('#view_mobile_number').val(res.mobile_number);
        $('#view_telephone_number').val(res.telephone_number);
        $('#view_email_address').val(res.email_address);
        $('#view_emergency_contact_person').val(res.emergency_contact_person);
        $('#view_emergency_contact_number').val(res.emergency_contact_number);
        $('#view_pwd_status').val(res.pwd_status);
        $('#view_pwd_id').val(res.pwd_id_number);
        $('#view_senior_status').val(res.senior_citizen_status);
        $('#view_senior_id').val(res.senior_id_number);
        $('#view_solo_parent').val(res.solo_parent_status);
        $('#is_4ps_member').val(res.is_4ps_member);
        $('#view_blood_type').val(res.blood_type);
        $('#view_voter').val(res.voter_status);

        const img = res.image_url ? '../uploads/' + res.image_url : '../assets/img/default.png';
        $('#view_imagePreview').attr('src', img);
      },
      error: function () {
        alert('Failed to fetch resident data.');
      }
    });
  });
});

// Auto-calculate residency duration
document.getElementById('residency_date').addEventListener('change', function () {
  const startDate = new Date(this.value);
  const now = new Date();
  if (startDate > now) {
    document.getElementById('years_of_residency').value = '';
    return;
  }

  const diffTime = Math.abs(now - startDate);
  const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  const years = Math.floor(totalDays / 365);
  const months = Math.floor((totalDays % 365) / 30);
  const days = totalDays % 30;
  document.getElementById('years_of_residency').value = `${years} year(s) ${months} month(s) ${days} day(s)`;
});

// Autocomplete for address
function setupAutocomplete(id) {
  const input = document.getElementById(id);
  if (!input) return;

  input.addEventListener("input", async function () {
    const value = input.value;
    if (value.length < 3) return;

    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(value)}&addressdetails=1&limit=5`;

    const res = await fetch(url);
    const results = await res.json();

    const datalistId = `${id}-suggestions`;
    let datalist = document.getElementById(datalistId);
    if (!datalist) {
      datalist = document.createElement("datalist");
      datalist.id = datalistId;
      document.body.appendChild(datalist);
      input.setAttribute("list", datalistId);
    }

    datalist.innerHTML = results.map(r => `<option value="${r.display_name}">`).join("");
  });
}

["pob_country", "pob_province", "pob_city", "pob_barangay", "addr_country", "addr_province", "addr_city", "addr_barangay"].forEach(setupAutocomplete);

// Enable/disable fields
function toggleFieldStatus(selectId, targetId) {
  const select = document.getElementById(selectId);
  const input = document.getElementById(targetId);
  if (!select || !input) return;

  const update = () => {
    input.disabled = select.value === "No" || select.value === "";
    if (input.disabled) input.value = "";
  };

  select.addEventListener("change", update);
  update();
}

toggleFieldStatus("pwd_status", "pwd_id_number");
toggleFieldStatus("senior_citizen_status", "senior_id_number");
toggleFieldStatus("view_pwd_status", "view_pwd_id");
toggleFieldStatus("view_senior_status", "view_senior_id");

function recheckAllGovernmentFields() {
  ['view_pwd_status', 'view_senior_status'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.dispatchEvent(new Event('change'));
  });
}

let isEditing = false;

document.getElementById('view_imagePreview').addEventListener('mouseenter', function () {
  this.style.cursor = isEditing ? 'pointer' : 'default';
});

$('#editBtn').on('click', function () {
  if (!isEditing) {
    const fullName =
      $('#view_first_name').val() + ' ' +
      ($('#view_middle_name').val() ? $('#view_middle_name').val().charAt(0).toUpperCase() + '.' : '') + ' ' +
      $('#view_last_name').val();

    $('#editPersonName').text(fullName.trim());

    const confirmEditModal = new bootstrap.Modal(document.getElementById('editConfirmModal'));
    confirmEditModal.show();

    $('#confirmEditBtn').off('click').on('click', function () {
      confirmEditModal.hide();

      $('#viewResidentModal input, #viewResidentModal select, #viewResidentModal textarea').prop('disabled', false);
      $('#view_imageInput').prop('disabled', false);
      $('#view_imagePreview').css('pointer-events', 'auto');
      recheckAllGovernmentFields();

      $('#editBtn').text('Save').removeClass('btn-primary').addClass('btn-success');
      isEditing = true;
    });

  } else {
    const formData = new FormData();
    formData.append('id', $('#view_id').val());
    formData.append('first_name', $('#view_first_name').val());
    formData.append('middle_name', $('#view_middle_name').val());
    formData.append('last_name', $('#view_last_name').val());
    formData.append('gender', $('#view_gender').val());
    formData.append('date_of_birth', $('#view_date_of_birth').val());
    formData.append('pob_country', $('#view_pob_country').val());
    formData.append('pob_province', $('#view_pob_province').val());
    formData.append('pob_city', $('#view_pob_city').val());
    formData.append('pob_barangay', $('#view_pob_barangay').val());
    formData.append('civil_status', $('#view_civil_status').val());
    formData.append('nationality', $('#view_nationality').val());
    formData.append('religion', $('#view_religion').val());
    formData.append('country', $('#view_country').val());
    formData.append('province', $('#view_province').val());
    formData.append('city', $('#view_city').val());
    formData.append('barangay', $('#view_barangay').val());
    formData.append('zipcode', $('#view_zipcode').val());
    formData.append('house_number', $('#view_house_number').val());
    formData.append('zone_purok', $('#view_zone_purok').val());
    formData.append('years_of_residency', $('#view_years_residency').val());
    formData.append('father_name', $('#view_father_name').val());
    formData.append('mother_name', $('#view_mother_name').val());
    formData.append('spouse_name', $('#view_spouse_name').val());
    formData.append('number_of_family_members', $('#view_family_members').val());
    formData.append('household_number', $('#view_household_number').val());
    formData.append('relationship_to_head', $('#view_relationship_head').val());
    formData.append('house_position', $('#view_house_position').val());
    formData.append('educational_attainment', $('#view_education').val());
    formData.append('occupation', $('#view_occupation').val());
    formData.append('monthly_income', $('#view_income').val());
    formData.append('mobile_number', $('#view_mobile_number').val());
    formData.append('telephone_number', $('#view_telephone_number').val());
    formData.append('email_address', $('#view_email_address').val());
    formData.append('emergency_contact_person', $('#view_emergency_contact_person').val());
    formData.append('emergency_contact_number', $('#view_emergency_contact_number').val());
    formData.append('pwd_status', $('#view_pwd_status').val());
    formData.append('pwd_id_number', $('#view_pwd_id').val());
    formData.append('senior_citizen_status', $('#view_senior_status').val());
    formData.append('senior_id_number', $('#view_senior_id').val());
    formData.append('solo_parent_status', $('#view_solo_parent').val());
    formData.append('is_4ps_member', $('#is_4ps_member').val());
    formData.append('blood_type', $('#view_blood_type').val());
    formData.append('voter_status', $('#view_voter').val());

    const croppedImageInput = document.getElementById('cropped_image_data');
    if (croppedImageInput && croppedImageInput.value) {
      formData.append('cropped_image', croppedImageInput.value);
    }
    const previewSrc = $('#view_imagePreview').attr('src');
    if (previewSrc) {
      const oldImageFilename = previewSrc.split('/').pop();
      formData.append('old_image', oldImageFilename);
    }

    $.ajax({
      url: '../database/resident_update.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.trim() === 'success') {
          alert('Resident information updated successfully.');
          $('#residentTable').load(location.href + " #residentTable>*", "");
          $('#viewResidentModal input, #viewResidentModal select, #viewResidentModal textarea').prop('disabled', true);
          $('#view_imageInput').prop('disabled', true);
          $('#view_imagePreview').css('pointer-events', 'none');
          $('#editBtn').text('Edit').removeClass('btn-success').addClass('btn-primary');
          isEditing = false;
        } else {
          showErrorModal(response);
        }
      },
      error: function () {
        alert('Failed to update resident.');
      }
    });
  }
});

// Image preview and cropping
$('#view_imagePreview').on('click', function () {
  if (!$('#view_imageInput').prop('disabled')) {
    $('#view_imageInput').click();
  }
});

const viewImageInput = document.getElementById('view_imageInput');
const viewImagePreview = document.getElementById('view_imagePreview');

viewImageInput.addEventListener('change', function (event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      viewImagePreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});

let cropper;
let targetPreview = null;

function handleImageCrop(event, previewId) {
  const file = event.target.files[0];
  targetPreview = document.getElementById(previewId);
  if (!file || !targetPreview) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const cropperImage = document.getElementById('cropperImage');
    cropperImage.src = e.target.result;

    const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
    cropperModal.show();

    document.getElementById('cropperModal').addEventListener('shown.bs.modal', () => {
      if (cropper) cropper.destroy();
      cropper = new Cropper(cropperImage, {
        aspectRatio: 1,
        viewMode: 1
      });
    }, { once: true });

    document.getElementById('cropImageBtn').onclick = () => {
      const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
      const base64Data = canvas.toDataURL('image/png');
      targetPreview.src = base64Data;
      document.getElementById('cropped_image_data').value = base64Data;
      cropperModal.hide();
    };
  };
  reader.readAsDataURL(file);
}

function showErrorModal(errorText) {
  document.getElementById('errorModalBody').textContent = errorText;
  const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
  errorModal.show();
}

// Click image to open file input (Add modal)
document.getElementById('imagePreview').addEventListener('click', function () {
  document.getElementById('imageInput').click();
});

// Delete resident
$(document).ready(function () {
  let selectedRow;

  $('.delete').on('click', function () {
    const id = $(this).data('id');
    const image = $(this).data('image');
    selectedRow = $(this).closest('tr');

    $('#delete_resident_id').val(id);
    $('#delete_resident_image').val(image);
  });

  $('#confirmDeleteBtn').on('click', function () {
    const id = $('#delete_resident_id').val();
    const image = $('#delete_resident_image').val();

    $.ajax({
      url: '../database/resident_delete.php',
      type: 'POST',
      data: { id: id, image: image },
      success: function (response) {
        if (response.trim() === 'success') {
          $('#deleteResidentModal').modal('hide');
          selectedRow.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          alert('Delete failed: ' + response);
        }
      },
      error: function () {
        alert('Error deleting resident.');
      }
    });
  });
});

// Search filter
$(document).ready(function () {
  $('#searchInput').on('keyup', function () {
    var value = $(this).val().toLowerCase();
    $('#residentTable tbody tr').filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// Modal close - reload
$('#viewResidentModal').on('hidden.bs.modal', function () {
  location.reload();
});
