<?php
include('sidebar.php');
include('../database/connection.php');

$query = "SELECT 
            r.id,
            CONCAT(res.last_name, ', ', res.first_name, ' ', LEFT(res.middle_name, 1), '.') AS full_name,
            s.service_name,
            r.purpose,
            r.request_date,
            r.status
          FROM requests r
          JOIN residents res ON r.resident_id = res.id
          JOIN services s ON r.service_id = s.id
          ORDER BY r.request_date DESC";

$result = mysqli_query($conn, $query);

?>

<!-- External Dependencies -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="../css/request.css" />

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="section-name">Barangay Request</div>
        <div class="notification-wrapper" id="notifBtn">
            <i class="bi bi-bell-fill" style="font-size: 35px;"></i>
            <span class="badge-number">4</span>
        </div>
    </div>
</nav>

<!-- Container for Add Button and Table -->
<div>
    <div class="search-container">
        <input type="text" class="search-input" id="serviceSearch" placeholder="Search service...">
    </div>
    <div class="action-buttons">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRequestModal">+ Add Request</button>
    </div>

    <!-- Request Table Section -->
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Resident Name</th>
                    <th>Service</th>
                    <th>Purpose</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody id="requestTableBody">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['service_name']}</td>
                            <td>{$row['purpose']}</td>
                            <td>{$row['request_date']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <button class='btn btn-primary btn-sm' onclick='printRequest({$row['id']})'>Print</button>
                                <button class='btn btn-danger btn-sm' onclick='declineRequest({$row['id']})'>Declined</button>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">📄 PDF Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="justify-content: center; display: flex; margin: 20px;">
                <div id="previewWrapper" style="position: relative;">
                    <canvas id="previewPdfCanvas" style="border: 3px solid black;"></canvas>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="printCanvas()">🖨️ Print</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Request Modal -->
<div class="modal fade" id="addRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addRequestForm" action="../database/request_add.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Resident Selection -->
                    <div id="residentSelectContainer">
                        <h6 class="mb-3 fw-bold">Select Resident</h6>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="residentSearch" placeholder="Search by name...">
                        </div>
                        <div id="residentList" class="row" style="max-height: 300px; overflow-y: auto;"></div>
                    </div>

                    <!-- After selecting resident -->
                    <div id="serviceSelectContainer" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <img id="selectedResidentImg" src="" class="rounded me-3" width="60" height="60" />
                            <div>
                                <div class="fw-bold" id="selectedResidentName"></div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-1" id="changeResidentBtn">Change Resident</button>
                            </div>
                        </div>

                        <input type="hidden" name="resident_id" id="selectedResidentId">

                        <div class="mb-3">
                            <label for="serviceDropdown" class="form-label">Select Service</label>
                            <select class="form-select" name="service_id" id="serviceDropdown" required>
                                <option value="">-- Select Service --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">✅ Request Submitted</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        The request has <strong>added successfully</strong>.
      </div>
    </div>
  </div>
</div>

<!-- Decline Confirmation Modal -->
<div class="modal fade" id="confirmDeclineModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">❗ Confirm Decline</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to decline this request?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmDeclineBtn" class="btn btn-danger">Yes, Decline</button>
      </div>
    </div>
  </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>
<script>
$(document).ready(function () {
    let residents = [];

    $('#addRequestModal').on('shown.bs.modal', function () {
        $('#residentSearch').val('');
        $('#residentList').empty();
        $('#residentSelectContainer').show();
        $('#serviceSelectContainer').hide();
        fetchResidents();
    });

    function fetchResidents() {
        $.ajax({
            url: '../database/fetch_residents.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                residents = data;
                renderResidentList(data);
            }
        });
    }

    function renderResidentList(residentData) {
        const container = $('#residentList');
        container.empty();

        residentData.forEach(res => {
            const card = `
                <div class="col-md-4 mb-2">
                    <div class="card resident-card p-2 text-center select-resident" style="cursor:pointer;" data-id="${res.id}" data-name="${res.full_name}" data-image="${res.image}">
                        <img src="${res.image}" class="rounded-circle mb-1" width="60" height="60">
                        <div>${res.full_name}</div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    $('#residentSearch').on('keyup', function () {
        const keyword = $(this).val().toLowerCase();
        const filtered = residents.filter(r => r.full_name.toLowerCase().includes(keyword));
        renderResidentList(filtered);
    });

    $(document).on('click', '.select-resident', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const image = $(this).data('image');

        $('#selectedResidentId').val(id);
        $('#selectedResidentImg').attr('src', image);
        $('#selectedResidentName').text(name);

        $('#residentSelectContainer').hide();
        $('#serviceSelectContainer').show();

        fetchServices();
    });

    $('#changeResidentBtn').on('click', function () {
        $('#residentSelectContainer').show();
        $('#serviceSelectContainer').hide();
    });

    function fetchServices() {
        $.ajax({
            url: '../database/fetch_services.php',
            method: 'GET',
            dataType: 'json',
            success: function (services) {
                let options = '<option value="">-- Select Service --</option>';
                services.forEach(s => {
                    options += `<option value="${s.id}">${s.service_name}</option>`;
                });
                $('#serviceDropdown').html(options);
            }
        });
    }
});

function printRequest(requestId) {
    $.ajax({
        url: '../database/print_request_overlay.php',
        method: 'GET',
        data: { id: requestId },
        dataType: 'json',
        success: function (response) {
            if (!response.success) {
                alert(response.message || "Failed to load request.");
                return;
            }

            const pdfFile = response.pdf_template;
            const layout = response.layout;

            $('#pdfPreviewModal').modal('show');

            const canvas = document.getElementById('previewPdfCanvas');
            const ctx = canvas.getContext('2d');
            const wrapper = document.getElementById('previewWrapper');
            wrapper.querySelectorAll('.preview-field').forEach(el => el.remove());

            pdfjsLib.getDocument('../pdf_templates/' + pdfFile).promise.then(pdf => {
                return pdf.getPage(1);
            }).then(page => {
                const viewport = page.getViewport({ scale: 1.5 });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                return page.render({ canvasContext: ctx, viewport }).promise.then(() => {
                    layout.forEach(field => {
                        const div = document.createElement('div');
                        div.className = 'preview-field';
                        div.innerText = field.text;
                        Object.assign(div.style, {
                            position: 'absolute',
                            left: field.x + 'px',
                            top: (field.y + 8) + 'px',
                            fontSize: field.fontSize + 'px',
                            fontFamily: field.fontFamily,
                            color: field.color,
                            fontWeight: field.fontWeight,
                            fontStyle: field.fontStyle,
                            textDecoration: field.textDecoration,
                            pointerEvents: 'none'
                        });
                        wrapper.appendChild(div);
                    });
                });
            });
        },
        error: function () {
            alert("❌ Error loading request PDF.");
        }
    });
}

// Show success modal if redirected with ?success=1
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === '1') {
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();

    // Optionally remove the query string from the URL
    history.replaceState(null, "", window.location.pathname);
}

let requestToDeclineId = null;

function declineRequest(requestId) {
    requestToDeclineId = requestId;
    const modal = new bootstrap.Modal(document.getElementById('confirmDeclineModal'));
    modal.show();
}

document.getElementById('confirmDeclineBtn').addEventListener('click', function () {
    if (!requestToDeclineId) return;

    $.ajax({
        url: '../database/request_decline.php',
        method: 'POST',
        data: { id: requestToDeclineId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                alert("Request declined successfully.");
                location.reload();
            } else {
                alert("Failed to decline request: " + response.message);
            }
        },
        error: function () {
            alert("❌ Error declining request.");
        }
    });

    const modalEl = document.getElementById('confirmDeclineModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
});

setInterval(() => {
            fetch('../database/fetch_request.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('requestTableBody').innerHTML = html;
                });
        }, 5000);
</script>