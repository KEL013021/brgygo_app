<?php 
include('sidebar.php');
include('../database/connection.php'); 
?>

<link rel="stylesheet" type="text/css" href="../css/services.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<title>Barangay Services - BRGY GO</title>

<style>
#pdfEditorWrapper {
  position: relative;
  display: inline-block;
}

#pdfCanvas {
  border: 1px solid #ccc;
}
</style>

<nav class="navbar">
    <div class="navbar-container">
        <div class="section-name">Barangay Services</div>
        <div class="notification-wrapper" id="notifBtn">
            <i class="bi bi-bell-fill" style="font-size: 35px;"></i>
            <span class="badge-number">4</span>
        </div>
    </div>
</nav>  

<div>
    <div class="search-container">
        <input type="text" class="search-input" id="serviceSearch" placeholder="Search service...">
    </div>
    <div class="action-buttons">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">+ Add Services</button>
    </div>

    <div class="table-container">
        <table class="custom-table" id="servicesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SERVICE NAME</th>
                    <th>DESCRIPTION</th>
                    <th>REQUIREMENTS</th>
                    <th>FEE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../database/connection.php'; // Adjust as needed

                $sql = "SELECT * FROM services ORDER BY id ASC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['service_name']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= htmlspecialchars($row['requirements']); ?></td>
                        <td><?= htmlspecialchars($row['service_fee']); ?></td>  
                        <td>
                            <button class="btn view" data-id="<?= $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#viewServiceModal">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn delete"
                                data-id="<?= $row['id'] ?>" 
        data-bs-toggle="modal" 
        data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="6" class="text-center">No service records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="../database/services_add.php" method="POST" enctype="multipart/form-data"> <!-- ✅ START FORM -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add Service</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Service name -->
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" name="service_name" required>
                        </div>

                        <!-- PDF Template Upload -->
                        <div class="mb-3">
                            <label for="pdfTemplate" class="form-label">PDF Template (Optional)</label>
                            <input type="file" class="form-control" id="pdfTemplate" name="pdf_template" accept="application/pdf">
                        </div>

                        <!-- Customize Button -->
                        <button type="button" class="btn btn-secondary mb-3" id="customizeLayoutBtn" disabled>
                            Customize PDF Layout
                        </button>

                        <!-- Fee & Requirements Row -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="serviceFee" class="form-label">Fee</label>
                                <input type="number" class="form-control" id="serviceFee" name="fee" step="0.01" required>
                            </div>
                            <div class="col-md-8">
                                <label for="requirements" class="form-label">Requirements</label>
                                <input type="text" class="form-control" id="requirements" name="requirements" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                        </div>

                        <!-- Hidden field to save layout JSON -->
                        <input type="hidden" name="pdf_layout_data" id="pdfLayoutData">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Service</button>
                    </div>
                </form> <!-- ✅ END FORM -->
            </div>
        </div>
    </div>

    <!-- PDF Layout Editor Modal -->
    <div class="modal fade" id="pdfLayoutEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Customize PDF Layout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="d-flex flex-column h-100">
                        <!-- 🔹 Toolbar -->
                        <div id="pdfToolbar" class="bg-light border-bottom p-1 d-flex align-items-center gap-3"
                            style="position: sticky; top: 0; z-index: 100; background-color: #f8f9fa;">
                            <div>
                                <label class="form-label mb-0 me-2">Font:</label>
                                <select id="fontFamily" class="form-select form-select-sm d-inline-block" style="width: 150px;">
                                    <option>Arial</option>
                                    <option>Calibri</option>
                                    <option>Times New Roman</option>
                                </select>
                            </div>
                            <div class="btn-group" role="group" aria-label="Text Styles">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnBold"><b>B</b></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnItalic"><i>I</i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUnderline"><u>U</u></button>
                            </div>
                            <div>
                                <label class="form-label mb-0 me-2">Size:</label>
                                <input type="number" id="fontSize" class="form-control form-control-sm d-inline-block" value="12" style="width: 80px;">
                            </div>
                            <div>
                                <label class="form-label mb-0 me-2">Color:</label>
                                <input type="color" id="fontColor" 
                                    class="form-control form-control-color form-control-sm d-inline-block align-middle" 
                                    value="#000000" 
                                    style="width: 50px; padding: 0;">
                            </div>
                            <button id="deleteFieldBtn" class="btn btn-danger btn-sm ms-auto">🗑️ Delete Selected</button>
                        </div>

                        <!-- 🔹 Editor Body -->
                        <div class="d-flex flex-grow-1" style="min-height: 450px; overflow: hidden;">
                            <!-- 🟦 Sidebar Fields -->
                            <div class="p-1 border-end bg-white" style="width: 250px; overflow-y: auto; max-height: 450px;">
                                <h6 class="fw-bold">📌 Fields</h6>

                                <div class="fw-bold mt-2 mb-1">Resident Info</div>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{full_name}}">Full Name</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{address}}">Address</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{birth_date}}">Birth Date</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{birth_place}}">Place of Birth</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{age}}">Age</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{civil_status}}">Civil Status</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{gender}}">Gender</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{religion}}">Religion</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{nationality}}">Nationality</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{contact_number}}">Contact Number</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{email}}">Email Address</button>

                                <div class="fw-bold mt-3 mb-1">Emergency Contact</div>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{emergency_name}}">Name</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{emergency_relation}}">Relation</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{emergency_number}}">Number</button>

                                <div class="fw-bold mt-3 mb-1">Barangay Officials</div>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{barangay_captain}}">Captain</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{barangay_secretary}}">Secretary</button>
                                <button class="btn btn-outline-secondary w-100 insert-field-btn mb-1" data-field="{{barangay_treasurer}}">Treasurer</button>
                            </div>

                            <!-- 🟧 Canvas Area -->
                            <div id="canvasScrollContainer" class="p-1 flex-grow-1 overflow-auto" style="max-height: 450px; position: relative;">
                                <div id="pdfEditorWrapper" style="position: relative; display: inline-block;">
                                    <canvas id="pdfCanvas"></canvas>
                                    <!-- fields will be appended here -->
                                </div>
                            </div>
                        </div> <!-- End editor body -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="saveLayoutBtn">Save Layout</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Service successfully added!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Already Exists Modal -->
    <div class="modal fade" id="existsModal" tabindex="-1" aria-labelledby="existsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="existsModalLabel">Duplicate Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Service with this name already exists.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">View Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Service Name -->
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" class="form-control" id="view_service_name" readonly>
                    </div>

                    <!-- Fee & Requirements Row -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Fee</label>
                            <input type="text" class="form-control" id="view_service_fee" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Requirements</label>
                            <input type="text" class="form-control" id="view_service_requirements" readonly>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="view_service_description" rows="2" readonly></textarea>
                    </div>
                    
                    <!-- Optional PDF upload for edit -->
                    <div class="mb-3" id="editPdfInputWrapper" hidden>
                        <label for="view_pdf_template" class="form-label">Replace PDF Template (optional)</label>
                        <input type="file" class="form-control" id="view_pdf_template" name="pdf_template" accept="application/pdf"  data-file="">
                        <div class="form-text">Current file: <span id="currentPdfFileName" class="text-primary"></span></div>
                    </div>

                    <!-- View PDF Layout Button -->
                    <button type="button" class="btn btn-secondary mb-2" id="previewCustomPdfBtn">
                        📄 View Custom PDF
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="editServiceBtn">Edit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">PDF Layout Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- ✅ Dark background and scrollable -->
                <div class="modal-body p-0" style="overflow-y: auto; max-height: 70vh; background-color: #6e6c6cff;">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 100%; padding: 2rem;">
                        <!-- ✅ Centered PDF canvas -->
                        <div id="previewWrapper"
                            style="position: relative; width: fit-content; margin: auto;">
                            <canvas id="previewPdfCanvas"
                                    style="display: block; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.5);"></canvas>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service?</p>
                <input type="hidden" id="deleteServiceId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>✅ Service has been deleted successfully.</p>
      </div>
    </div>
  </div>
</div>



</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../ajax/services.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    </script>
    <script>
        let pdfDoc = null;
        let canvas = document.getElementById('pdfCanvas');
        let ctx = canvas.getContext('2d');
        let selectedField = null;
        let activeEl = null;
        let offsetX, offsetY;

        // Render PDF on canvas
        document.getElementById('pdfTemplate').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type === 'application/pdf') {
                const fileReader = new FileReader();
                fileReader.onload = function() {
                    const typedarray = new Uint8Array(this.result);
                    pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                        pdfDoc = pdf;
                        return pdf.getPage(1);
                    }).then(function(page) {
                        const viewport = page.getViewport({ scale: 1.5 });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({ canvasContext: ctx, viewport: viewport });
                        document.getElementById('customizeLayoutBtn').disabled = false;
                    });
                };
                fileReader.readAsArrayBuffer(file);
            }
        });

        // Open Layout Editor Modal
        document.getElementById('customizeLayoutBtn').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('pdfLayoutEditorModal'));
            modal.show();
        });

        // Save Layout
        document.getElementById('saveLayoutBtn').addEventListener('click', function() {
            const fields = document.querySelectorAll('.draggable-field');
            const layout = [];

            fields.forEach(el => {
                const computedStyle = window.getComputedStyle(el);
                layout.push({
                    text: el.innerText,
                    x: parseFloat(el.style.left),
                    y: parseFloat(el.style.top),
                    fontSize: parseInt(computedStyle.fontSize),
                    fontFamily: computedStyle.fontFamily,
                    color: computedStyle.color,
                    fontWeight: computedStyle.fontWeight,
                    fontStyle: computedStyle.fontStyle,
                    textDecoration: computedStyle.textDecorationLine // use `.textDecorationLine` to get `underline`
                });
            });

            document.getElementById('pdfLayoutData').value = JSON.stringify(layout);
            bootstrap.Modal.getInstance(document.getElementById('pdfLayoutEditorModal')).hide();
        });

        // Mouse drag logic
        document.addEventListener('mousedown', function (e) {
            if (e.target.classList.contains('draggable-field')) {
                // 🔒 Check if mouse is on the resize edge (right side only)
                const bounds = e.target.getBoundingClientRect();
                const edgeThreshold = 10; // px from the edge to consider as "resize zone"
                const isResizing = (bounds.right - e.clientX) < edgeThreshold;

                if (isResizing) {
                    // Skip dragging if user is resizing
                    return;
                }

                // ✅ Normal drag logic
                activeEl = e.target;
                const rect = activeEl.getBoundingClientRect();
                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;
                e.preventDefault();
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (activeEl) {
                const container = document.getElementById('pdfEditorWrapper');
                const containerRect = container.getBoundingClientRect();

                const x = e.clientX - containerRect.left - offsetX;
                const y = e.clientY - containerRect.top - offsetY;

                activeEl.style.left = x + 'px';
                activeEl.style.top = y + 'px';
            }
        });

        document.addEventListener('mouseup', function() {
            activeEl = null;
        });

        // Insert field from sidebar
        // Insert field from sidebar
document.querySelectorAll('.insert-field-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const fieldValue = this.getAttribute('data-field');
        const div = document.createElement('div');
        div.className = 'draggable-field';
        div.contentEditable = true;
        div.innerText = fieldValue;
        div.style.position = 'absolute';
        div.style.left = '50px';
        div.style.top = '50px';
        div.style.padding = '2px 5px';
        div.style.border = '1px dashed #000';
        div.style.background = '#f9f9f9';
        div.style.cursor = 'move';
        div.style.fontFamily = document.getElementById('fontFamily').value;
        div.style.fontSize = document.getElementById('fontSize').value + 'px';
        div.style.color = document.getElementById('fontColor').value;
        div.style.zIndex = 10;

        // ✅ Make it manually resizable
        div.style.resize = 'horizontal';
        div.style.overflow = 'hidden';
        div.style.minWidth = '50px';
        div.style.maxWidth = '400px';
        div.style.whiteSpace = 'nowrap'; // or 'normal' kung gusto mo wrapping

        // ✅ Center text within box
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.justifyContent = 'center';
        div.style.textAlign = 'center';
        div.style.textOverflow = 'ellipsis';

        // Click to select field
        div.addEventListener('click', function (e) {
            e.stopPropagation();
            if (selectedField) selectedField.style.border = '1px dashed #000';
            selectedField = div;
            div.style.border = '2px solid red';
        });

        document.getElementById('pdfEditorWrapper').appendChild(div);
    });
});


        // Deselect field on wrapper click
        document.getElementById('pdfEditorWrapper').addEventListener('click', function () {
            if (selectedField) {
                selectedField.style.border = '1px dashed #000';
                selectedField = null;
            }
        });

        // Delete selected field
        document.getElementById('deleteFieldBtn').addEventListener('click', () => {
            if (selectedField) {
                selectedField.remove();
                selectedField = null;
            }
        });

        // Font controls
        document.getElementById('fontFamily').addEventListener('change', (e) => {
            if (selectedField) selectedField.style.fontFamily = e.target.value;
        });
        document.getElementById('fontSize').addEventListener('change', (e) => {
            if (selectedField) selectedField.style.fontSize = e.target.value + 'px';
        });
        document.getElementById('fontColor').addEventListener('input', (e) => {
            if (selectedField) selectedField.style.color = e.target.value;
        });

        // Bold
        document.getElementById('btnBold').addEventListener('click', function () {
            if (selectedField) {
                const isBold = selectedField.style.fontWeight === 'bold';
                selectedField.style.fontWeight = isBold ? 'normal' : 'bold';
                this.classList.toggle('active', !isBold);
            }
        });

        // Italic
        document.getElementById('btnItalic').addEventListener('click', function () {
            if (selectedField) {
                const isItalic = selectedField.style.fontStyle === 'italic';
                selectedField.style.fontStyle = isItalic ? 'normal' : 'italic';
                this.classList.toggle('active', !isItalic);
            }
        });

        // Underline
        document.getElementById('btnUnderline').addEventListener('click', function () {
            if (selectedField) {
                const isUnderlined = selectedField.style.textDecoration === 'underline';
                selectedField.style.textDecoration = isUnderlined ? 'none' : 'underline';
                this.classList.toggle('active', !isUnderlined);
            }
        });
        
        // 👁️ VIEW SERVICE MODAL
        $(document).on('click', '.btn.view', function () {
            const serviceId = $(this).data('id');

            $.ajax({
                url: '../database/service_get_single.php',
                method: 'GET',
                data: { id: serviceId },
                dataType: 'json',
                success: function (data) {
                    if (!data.success) return alert('Service not found.');

                    const service = data.service;

                    // Populate form
                    $('#view_service_name').val(service.service_name);
                    $('#view_service_fee').val(service.service_fee);
                    $('#view_service_requirements').val(service.requirements);
                    $('#view_service_description').val(service.description);

                    // Extract PDF file name only
                    const pdfFile = service.pdf_template ? service.pdf_template.split('/').pop() : '';
                    const layoutData = service.pdf_layout_data || '[]';
                    $('#currentPdfFileName').text(pdfFile || 'None');

                    // Set attributes for later
                    $('#previewCustomPdfBtn').data({ pdf: pdfFile, layout: layoutData });
                    $('#editServiceBtn').data({ id: serviceId, pdf: pdfFile, layout: layoutData });

                    // Reset file input's data-file for fallback
                    $('#view_pdf_template').attr('data-file', pdfFile);
                },
                error: function () {
                    alert('Error fetching service data.');
                }
            });
        });

        // 👁️ PREVIEW PDF
        $('#previewCustomPdfBtn').on('click', function () {
            const layout = $(this).data('layout');
            const pdfFile = $(this).data('pdf');

            if (!pdfFile || !layout) {
                alert("Missing PDF or layout.");
                return;
            }

            $('#pdfPreviewModal').modal('show');
            document.querySelectorAll('#previewWrapper .preview-field').forEach(el => el.remove());

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

            pdfjsLib.getDocument('../pdf_templates/' + pdfFile).promise.then(pdf => {
                return pdf.getPage(1);
            }).then(page => {
                const canvas = document.getElementById('previewPdfCanvas');
                const ctx = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: 1.5 });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                return page.render({ canvasContext: ctx, viewport }).promise.then(() => {
                    const fields = JSON.parse(layout || '[]');
                    const wrapper = document.getElementById('previewWrapper');

                    fields.forEach(field => {
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
            }).catch(err => {
                console.error(err);
                alert("PDF failed to load.");
            });
        });

        // ✏️ EDIT MODE
        $('#editServiceBtn').on('click', function () {
            const serviceId = this.dataset.id;
            const layout = this.dataset.layout || '[]';
            const pdfFile = this.dataset.pdf || '';

            // Enable text fields
            ['view_service_name', 'view_service_fee', 'view_service_requirements', 'view_service_description'].forEach(id => {
                document.getElementById(id).readOnly = false;
            });

            // Show file input
            $('#editPdfInputWrapper').removeAttr('hidden');

            // Replace Preview Button with Edit Layout Button
            const previewBtn = $('#previewCustomPdfBtn');
            const editLayoutBtn = $('<button>', {
                id: 'editLayoutBtn',
                class: 'btn btn-warning mb-2',
                text: '🛠️ Edit Layout',
                'data-pdf': pdfFile,
                'data-layout': layout
            });

            previewBtn.replaceWith(editLayoutBtn);

            // 🛠️ Edit Layout Button Logic
            editLayoutBtn.on('click', function () {
                const layoutRaw = this.dataset.layout;
                const pdfFile = this.dataset.pdf;

                const fileInput = document.getElementById('view_pdf_template');
                let pdfUrl = '';

                if (fileInput && fileInput.files.length > 0) {
                    pdfUrl = URL.createObjectURL(fileInput.files[0]);
                } else {
                    const fallback = fileInput.getAttribute('data-file');
                    if (fallback && fallback !== 'undefined') {
                        pdfUrl = '../pdf_templates/' + fallback;
                    } else if (pdfFile && pdfFile !== 'undefined') {
                        pdfUrl = '../pdf_templates/' + pdfFile;
                    } else {
                        alert("⚠️ No PDF file available for editing.");
                        return;
                    }
                }

                try {
                    const parsedLayout = JSON.parse(layoutRaw || '[]');
                    loadPdfForEditing(pdfUrl, parsedLayout);
                    const modal = new bootstrap.Modal(document.getElementById('pdfLayoutEditorModal'));
                    modal.show();
                } catch (e) {
                    console.error("Error parsing layout:", e);
                    alert("❌ Invalid layout data.");
                }
            });

            // Replace Edit button with Save Changes
            const saveBtn = $('<button>', {
                id: 'saveServiceChangesBtn',
                class: 'btn btn-success',
                text: '💾 Save Changes'
            });

            saveBtn.on('click', function () {
                const formData = new FormData();
                formData.append('id', serviceId);
                formData.append('service_name', $('#view_service_name').val());
                formData.append('service_fee', $('#view_service_fee').val());
                formData.append('requirements', $('#view_service_requirements').val());
                formData.append('description', $('#view_service_description').val());

                const fileInput = document.getElementById('view_pdf_template');
                if (fileInput && fileInput.files.length > 0) {
                    formData.append('pdf_templates', fileInput.files[0]);
                }

                $.ajax({
                    url: '../database/service_update.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        alert("✅ Service updated.");
                        location.reload();
                    },
                    error: function () {
                        alert("❌ Failed to update service.");
                    }
                });
            });

            $(this).replaceWith(saveBtn);
        });

        // 🧠 Load PDF and layout for editing
        function loadPdfForEditing(pdfUrl, layoutData) {
            const canvas = document.getElementById('pdfCanvas');
            const ctx = canvas.getContext('2d');
            const wrapper = document.getElementById('pdfEditorWrapper');

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            wrapper.querySelectorAll('.draggable-field').forEach(el => el.remove());

            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                return pdf.getPage(1);
            }).then(page => {
                const viewport = page.getViewport({ scale: 1.5 });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                return page.render({ canvasContext: ctx, viewport }).promise.then(() => {
                    layoutData.forEach(field => {
                        const div = document.createElement('div');
                        div.className = 'draggable-field';
                        div.contentEditable = true;
                        div.innerText = field.text;

                        Object.assign(div.style, {
                            position: 'absolute',
                            left: field.x + 'px',
                            top: field.y + 'px',
                            fontSize: field.fontSize + 'px',
                            fontFamily: field.fontFamily,
                            color: field.color,
                            fontWeight: field.fontWeight,
                            fontStyle: field.fontStyle,
                            textDecoration: field.textDecoration,
                            padding: '2px 5px',
                            border: '1px dashed #000',
                            background: '#f9f9f9',
                            cursor: 'move',
                            resize: 'horizontal',
                            overflow: 'auto',
                            minWidth: '50px',
                            maxWidth: '400px',
                            whiteSpace: 'nowrap',
                            zIndex: 10
                        });

                        // Allow selection
                        div.addEventListener('click', function (e) {
                            e.stopPropagation();
                            if (selectedField) selectedField.style.border = '1px dashed #000';
                            selectedField = div;
                            div.style.border = '2px solid red';
                        });

                        wrapper.appendChild(div);
                    });
                });
            }).catch(err => {
                console.error('Failed to load PDF for editing:', err);
                alert('❌ Failed to load PDF for editing.');
            });
        }

$(document).ready(function () {
  // Set ID to modal when delete button is clicked
  $(document).on('click', '.btn.delete', function () {
    const id = $(this).data('id');
    $('#deleteServiceId').val(id);
  });

  // Handle confirm delete
  $('#confirmDeleteBtn').on('click', function () {
    const serviceId = $('#deleteServiceId').val();

    $.ajax({
      url: '../database/services_delete.php',
      method: 'POST',
      data: { id: serviceId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#deleteModal').modal('hide');
          $('#successModal').modal('show');
        } else {
          alert("❌ Failed: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log("XHR Response:", xhr.responseText);
        console.log("Status:", status);
        console.log("Error:", error);
        alert("❌ Server Error:\n" + xhr.responseText);
      }
    });
  });

  // 🔄 Reload the table body after the success modal is closed
  $('#successModal').on('hidden.bs.modal', function () {
    location.reload();
  });
});

    </script>