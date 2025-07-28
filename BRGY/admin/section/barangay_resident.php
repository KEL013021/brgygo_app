    <?php include('sidebar.php'); ?>

    <link rel="stylesheet" type="text/css" href="../css/barangay_residents.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <title>Barangay Resident - BRGY GO</title>

    <nav class="navbar">
      <div class="navbar-container">
        <div class="section-name">Barangay Resident</div>

        <div class="notification-wrapper" id="notifBtn"> <!-- ✅ Now it's inside -->
          <i class="bi bi-bell-fill" style="font-size: 35px;"></i>
          <span class="badge-number">4</span>
        </div>
      </div>
    </nav>  

    <div>
      <div class="search-container">
          <input type="text" class="form-control search-input" id="searchInput" placeholder="Search...">
      </div>
      <div>
        <button class="add-resident-btn" data-bs-toggle="modal" data-bs-target="#addResidentModal">+ Add Resident</button>
      </div>


      <div class="table-container">
      <table class="custom-table" id="residentTable"   >
        <thead>
          <tr>
            <th>ID</th>
            <th>IMAGE</th>
            <th>FULLNAME</th>
            <th>GMAIL</th>
            <th>PHONE  NUMBER</th>
            <th>HOUSE  POSITION</th>
            <th>HOUSE  NO</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <?php
            include '../database/connection.php'; // Adjust path as needed

            $sql = "SELECT id, image_url, first_name, middle_name, last_name, email_address, mobile_number, house_position, house_number FROM residents ORDER BY last_name ASC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)):
                // Handle image preview fallback
                $imagePath = !empty($row['image_url']) ? '../uploads/' . $row['image_url'] : '../assets/img/default.png';
            ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><img src="<?= $imagePath; ?>" alt="Profile" class="table-img"></td>
              <td>
                <?= htmlspecialchars(
                $row['last_name'] . ', ' . 
                $row['first_name'] . 
                (trim($row['middle_name']) !== '' ? ' ' . strtoupper(substr(trim($row['middle_name']), 0, 1)) . '.' : '')
              ); ?>
              </td>
              <td><?= htmlspecialchars($row['email_address']); ?></td>
              <td><?= htmlspecialchars($row['mobile_number']); ?></td>
              <td><?= htmlspecialchars($row['house_position']); ?></td>
              <td><?= htmlspecialchars($row['house_number']); ?></td>
              <td>
                <button class="btn view" data-id="<?= $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#viewResidentModal">
                  <i class="bi bi-eye"></i>
                </button>
                
                <button class="btn delete" 
                        data-id="<?= $row['id']; ?>" 
                        data-image="<?= $row['image_url']; ?>" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteResidentModal">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <?php
              endwhile;
            else:
            ?>
            <tr>
              <td colspan="8" class="text-center">No resident records found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <!-- Add Resident Modal -->
    <div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addResidentLabel">Add New Resident</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="../database/resident_add.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body"  style="max-height: 72vh; overflow-y: auto;">
              
              <!-- SECTION: PERSONAL INFORMATION -->
              <h5 class="text-primary mt-3">Personal Information</h5>
              <div class="row g-3">
                <div class="col-md-3 text-center">
                  <div id="imageWrapper" style="border: 2px dashed #ccc; border-radius: 5px; height: 300px; min-height: 300px; overflow: hidden; position: relative;">
                    <img id="imagePreview" src="../image/Logo.png" alt="Preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; cursor: pointer;">
                    <input type="file" id="imageInput" onchange="handleImageCrop(event, 'imagePreview')" accept="image/*" hidden>
                    <input type="hidden" name="cropped_image_data" id="cropped_image_data">
                  </div>
                  <label class="form-label">Profile Picture</label>
                </div>
                <div class="col-md-9">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">First Name</label>
                      <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Middle Name</label>
                      <input type="text" class="form-control" name="middle_name">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Last Name</label>
                      <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Gender</label>
                      <select class="form-select" name="gender">
                        <option value="">-- Select Gender --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Date of Birth</label>
                      <input type="date" class="form-control" name="date_of_birth" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Place of Birth - Country</label>
                      <input type="text" class="form-control" name="pob_country" placeholder="e.g., Philippines" id="pob_country">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Place of Birth - Province</label>
                      <input type="text" class="form-control" name="pob_province" placeholder="e.g., Cavite" id="pob_province">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Place of Birth - City</label>
                      <input type="text" class="form-control" name="pob_city" placeholder="e.g., Imus" id="pob_city">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Place of Birth - Barangay</label>
                      <input type="text" class="form-control" name="pob_barangay" placeholder="e.g., Malagasang" id="pob_barangay">
                    </div>
                  </div>
                </div>
              </div>
              <!-- SECTION: CIVIL STATUS & RELIGION -->
              <h5 class="text-primary mt-5">Civil Status & Religion</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Civil Status</label>
                  <select class="form-select" name="civil_status">
                    <option value="">-- Select Status --</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nationality</label>
                  <input type="text" class="form-control" name="nationality">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Religion</label>
                  <input type="text" class="form-control" name="religion">
                </div>
              </div>

              <!-- SECTION: CURRENT ADDRESS -->
              <h5 class="text-primary mt-5">Current Address</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Country</label>
                  <input type="text" class="form-control" name="country" placeholder="e.g., Philippines" id="addr_country">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Province</label>
                  <input type="text" class="form-control" name="province" placeholder="e.g., Cavite" id="addr_province">
                </div>
                <div class="col-md-4">
                  <label class="form-label">City</label>
                  <input type="text" class="form-control" name="city" placeholder="e.g., Bacoor" id="addr_city">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Barangay</label>
                  <input type="text" class="form-control" name="barangay" placeholder="e.g., Molino I" id="addr_barangay">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Zipcode</label>
                  <input type="text" class="form-control" name="zipcode" placeholder="e.g., 4102">
                </div>
                <div class="col-md-4">
                  <label class="form-label">House Number</label>
                  <input type="text" class="form-control" name="house_number" placeholder="e.g., 123">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Zone/Purok</label>
                  <input type="text" class="form-control" name="zone_purok" placeholder="e.g., Zone 5">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Date of Residency</label>
                  <input type="date" class="form-control" name="residency_date" id="residency_date">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Years of Residency</label>
                  <input type="text" class="form-control" name="years_of_residency" id="years_of_residency" readonly>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Type of Residency</label>
                  <select class="form-select" name="residency_type" required>
                    <option value="">-- Select Residency Type --</option>
                    <option value="Non-Migrant">Non-Migrant</option>
                    <option value="Migrant">Migrant</option>
                    <option value="Transient">Transient</option>
                  </select>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Previous Address</label>
                  <input type="text" class="form-control" name="previous_address" placeholder="e.g., 456 Old Street, Cavite City">
                </div>
              </div>

              <!-- SECTION: FAMILY BACKGROUND -->
              <h5 class="text-primary mt-5">Family Background</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Father's Name</label>
                  <input type="text" class="form-control" name="father_name">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Mother's Name</label>
                  <input type="text" class="form-control" name="mother_name">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Spouse's Name</label>
                  <input type="text" class="form-control" name="spouse_name">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Number of Family Members</label>
                  <input type="number" class="form-control" name="number_of_family_members">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Household Number</label>
                  <input type="text" class="form-control" name="household_number">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Relationship to Head</label>
                  <input type="text" class="form-control" name="relationship_to_head">
                </div>
                <div class="col-md-4">
                  <label class="form-label">House Position</label>
                  <input type="text" class="form-control" name="house_position">
                </div>
              </div>

              <!-- SECTION: EDUCATION & EMPLOYMENT -->
              <h5 class="text-primary mt-5">Education & Employment</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Educational Attainment</label>
                  <input type="text" class="form-control" name="educational_attainment">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Current School</label>
                  <input type="text" class="form-control" name="current_school">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Occupation</label>
                  <input type="text" class="form-control" name="occupation">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Monthly Income</label>
                  <input type="number" class="form-control" name="monthly_income">
                </div>
              </div>

              <!-- SECTION: CONTACT INFORMATION -->
              <h5 class="text-primary mt-5">Contact Information</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Mobile Number</label>
                  <input type="text" class="form-control" name="mobile_number">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Telephone Number</label>
                  <input type="text" class="form-control" name="telephone_number">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Email Address</label>
                  <input type="email" class="form-control" name="email_address">
                </div>
              </div>

              <!-- SECTION: EMERGENCY CONTACT -->
              <h5 class="text-primary mt-5">Emergency Contact</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Emergency Contact Person</label>
                  <input type="text" class="form-control" name="emergency_contact_person">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Emergency Contact Number</label>
                  <input type="text" class="form-control" name="emergency_contact_number">
                </div>
              </div>

              <!-- SECTION: GOVERNMENT INFO -->
              <h5 class="text-primary mt-5">Government Information</h5>
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">PWD Status</label>
                  <select class="form-select" name="pwd_status">
                    <option value="">-- Select Status --</option>
                    <option>No</option>
                    <option>Yes</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">PWD ID Number</label>
                  <input type="text" class="form-control" name="pwd_id_number">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Senior Citizen</label>
                  <select class="form-select" name="senior_citizen_status">
                    <option value="">-- Select Status --</option>
                    <option>No</option>
                    <option>Yes</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Senior ID Number</label>
                  <input type="text" class="form-control" name="senior_id_number">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Solo Parent</label>
                  <select class="form-select" name="solo_parent_status">
                    <option value="">-- Select Status --</option>
                    <option>No</option>
                    <option>Yes</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">4Ps Member</label>
                  <select class="form-select" name="is_4ps_member">
                    <option value="">-- Select Status --</option>
                    <option>No</option>
                    <option>Yes</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Blood Type</label>
                  <input type="text" class="form-control" name="blood_type">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Voter Status</label>
                  <select class="form-select" name="voter_status" required>
                    <option value="">-- Select Voter Status --</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary btn-lg px-4 py-2" style="font-size: 16px">Save</button>
              <button type="button" class="btn btn-danger btn-lg px-4 py-2" data-bs-dismiss="modal" style="font-size: 16px">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<!-- View Resident Modal -->
<div class="modal fade mt" id="viewResidentModal" tabindex="-1" aria-labelledby="viewResidentLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewResidentLabel">Resident Details</h5>
        <input type="hidden" id="view_id" name="id">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form>
        <div class="modal-body" style="max-height: 72vh; overflow-y: auto;">

          <h5 class="text-primary mt-3">Personal Information</h5>
          <div class="row g-3">
            <div class="col-md-3 text-center">
              <div style="border: 2px dashed #ccc; border-radius: 5px; height: 300px; overflow: hidden; position: relative;">
                <img id="view_imagePreview" src="../assets/img/default.png" alt="Preview"
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                <input type="file" id="view_imageInput" name="image" onchange="handleImageCrop(event, 'view_imagePreview')" accept="image/*" hidden disabled>
              </div>
              <label class="form-label">Profile Picture</label>
            </div>
            <div class="col-md-9">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" id="view_first_name" name="first_name" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="view_middle_name" name="middle_name" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="view_last_name" name="last_name" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select class="form-select" id="view_gender" name="gender" disabled>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" class="form-control" id="view_date_of_birth" name="date_of_birth" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Place of Birth - Country</label>
                  <input type="text" class="form-control" id="view_pob_country" name="pob_country" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Place of Birth - Province</label>
                  <input type="text" class="form-control" id="view_pob_province" name="pob_province" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Place of Birth - City</label>
                  <input type="text" class="form-control" id="view_pob_city" name="pob_city" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Place of Birth - Barangay</label>
                  <input type="text" class="form-control" id="view_pob_barangay" name="pob_barangay" disabled>
                </div>
              </div>
            </div>
          </div>

          <h5 class="text-primary mt-5">Civil Status & Religion</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Civil Status</label>
              <select class="form-select" id="view_civil_status" name="civil_status" disabled>
                <option value="">-- Select Status --</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Nationality</label>
              <input type="text" class="form-control" id="view_nationality" name="nationality" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Religion</label>
              <input type="text" class="form-control" id="view_religion" name="religion" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Current Address</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Country</label>
              <input type="text" class="form-control" id="view_country" name="country" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Province</label>
              <input type="text" class="form-control" id="view_province" name="province" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" class="form-control" id="view_city" name="city" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Barangay</label>
              <input type="text" class="form-control" id="view_barangay" name="barangay" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Zipcode</label>
              <input type="text" class="form-control" id="view_zipcode" name="zipcode" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">House Number</label>
              <input type="text" class="form-control" id="view_house_number" name="house_number" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Zone/Purok</label>
              <input type="text" class="form-control" id="view_zone_purok" name="zone_purok" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Date of Residency</label>
              <input type="date" class="form-control" id="view_residency_date" name="residency_date" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Years of Residency</label>
              <input type="text" class="form-control" id="view_years_residency" name="years_of_residency" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Type of Residency</label>
              <input type="text" class="form-control" id="view_residency_type" name="residency_type" disabled>
            </div>
            <div class="col-md-8">
              <label class="form-label">Previous Address</label>
              <input type="text" class="form-control" id="view_previous_address" name="previous_address" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Family Background</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Father's Name</label>
              <input type="text" class="form-control" id="view_father_name" name="father_name" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Mother's Name</label>
              <input type="text" class="form-control" id="view_mother_name" name="mother_name" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Spouse's Name</label>
              <input type="text" class="form-control" id="view_spouse_name" name="spouse_name" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Number of Family Members</label>
              <input type="text" class="form-control" id="view_family_members" name="number_of_family_members" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Household Number</label>
              <input type="text" class="form-control" id="view_household_number" name="household_number" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Relationship to Head</label>
              <input type="text" class="form-control" id="view_relationship_head" name="relationship_to_head" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">House Position</label>
              <input type="text" class="form-control" id="view_house_position" name="house_position" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Education & Employment</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Educational Attainment</label>
              <input type="text" class="form-control" id="view_education" name="educational_attainment" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Current School</label>
              <input type="text" class="form-control" id="view_current_school" name="current_school" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Occupation</label>
              <input type="text" class="form-control" id="view_occupation" name="occupation" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Monthly Income</label>
              <input type="text" class="form-control" id="view_income" name="monthly_income" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Contact Information</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Mobile Number</label>
              <input type="text" class="form-control" id="view_mobile_number" name="mobile_number" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Telephone Number</label>
              <input type="text" class="form-control" id="view_telephone_number" name="telephone_number" disabled>
            </div>
            <div class="col-md-4">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" id="view_email_address" name="email_address" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Emergency Contact</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Person</label>
              <input type="text" class="form-control" id="view_emergency_contact_person" name="emergency_contact_person" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Number</label>
              <input type="text" class="form-control" id="view_emergency_contact_number" name="emergency_contact_number" disabled>
            </div>
          </div>

          <h5 class="text-primary mt-5">Government Information</h5>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">PWD</label>
              <select class="form-select" id="view_pwd_status" name="pwd_status" disabled>
                <option value="">-- Select --</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">PWD ID Number</label>
              <input type="text" class="form-control" id="view_pwd_id" name="pwd_id_number" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">Senior Citizen</label>
              <select class="form-select" id="view_senior_status" name="senior_citizen_status" disabled>
                <option value="">-- Select --</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Senior ID Number</label>
              <input type="text" class="form-control" id="view_senior_id" name="senior_id_number" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">Solo Parent</label>
              <input type="text" class="form-control" id="view_solo_parent" name="solo_parent_status" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">4Ps Member</label>
              <select class="form-select" id="view_4ps_status" name="is_4ps_member" disabled>
                <option value="">-- Select --</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Blood Type</label>
              <input type="text" class="form-control" id="view_blood_type" name="blood_type" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">Voter Status</label>
              <input type="text" class="form-control" id="view_voter" name="voter_status" disabled>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-lg px-4 py-2" id="editBtn" style="font-size: 16px">Edit</button>
          <button type="button" class="btn btn-danger btn-lg px-4 py-2" data-bs-dismiss="modal" style="font-size: 16px">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Confirmation Modal -->  
<div class="modal fade" id="editConfirmModal" tabindex="-1" aria-labelledby="editConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning">
        <h5 class="modal-title fw-bold" id="editConfirmModalLabel">Confirm Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body fs-5 text-center">
        Are you sure you want to edit this person:  
        <strong id="editPersonName" class="d-block mt-2 text-primary"></strong>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal" style="font-size: 20px">Cancel</button>
        <button type="button" class="btn btn-warning px-4" id="confirmEditBtn" style="font-size: 20px">Yes, Edit</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Update Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="errorModalBody" style="white-space: pre-wrap; font-family: monospace;"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="width: 110%">
      <div class="modal-header">
        <h5 class="modal-title">Crop Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="cropperImage" class="img-fluid" style="max-height: 65vh; object-fit: contain;">

      </div>
      <div class="modal-footer">
        <button type="button" id="cropImageBtn" class="btn btn-success" style="font-size: 18px; padding: 5px 10px;">Crop & Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteResidentModal" tabindex="-1" aria-labelledby="deleteResidentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteResidentModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this resident?</p>
        <input type="hidden" id="delete_resident_id">
        <input type="hidden" id="delete_resident_image">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="font-size: 18px; padding: 5px 10px; ">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="font-size: 18px; padding: 5px 10px; ">Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="../ajax/barangay_resident.js"></script>
