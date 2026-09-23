<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Neophoto</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/dashboard/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/dashboard/assets/css/style.css">
    <!-- End layout styles -->
       
  </head>
  <body>
       @livewireStyles
    @livewireScripts
 
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <a class="navbar-brand brand-logo" href="#">NEOPHOTO</a>
          <a class="navbar-brand brand-logo-mini" href="#"><img src="assets/dashboard/assets/images/logo-mini.svg" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <div class="search-field d-none d-md-block">
            <form class="d-flex align-items-center h-100" action="#">
              <div class="input-group">
                <div class="input-group-prepend bg-transparent">
                  <i class="input-group-text border-0 mdi mdi-magnify"></i>
                </div>
                <input type="text" class="form-control bg-transparent border-0" placeholder="Search projects">
              </div>
            </form>
          </div>
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="nav-profile-img">
                  <img src="assets/dashboard/assets/images/faces/face1.png" alt="image">
                  <span class="availability-status online"></span>
                </div>
                <div class="nav-profile-text">
                  <p class="mb-1 text-black">{{auth()->user()->name}}</p>
                </div>
              </a>
              <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="#">
                  <i class="mdi mdi-cached me-2 text-success"></i> Activity Log </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                  <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
              </div>
            </li>
            <li class="nav-item d-none d-lg-block full-screen-link">
              <a class="nav-link">
                <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-email-outline"></i>
                <span class="count-symbol bg-warning"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                <h6 class="p-3 mb-0">Messages</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/dashboard/assets/images/faces/face4.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Mark send you a message</h6>
                    <p class="text-gray mb-0"> 1 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/dashboard/assets/images/faces/face2.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Cregh send you a message</h6>
                    <p class="text-gray mb-0"> 15 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/dashboard/assets/images/faces/face3.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Profile picture updated</h6>
                    <p class="text-gray mb-0"> 18 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <h6 class="p-3 mb-0 text-center">4 new messages</h6>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                <i class="mdi mdi-bell-outline"></i>
                <span class="count-symbol bg-danger"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0">Notifications</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      <i class="mdi mdi-calendar"></i>
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject font-weight-normal mb-1">Event today</h6>
                    <p class="text-gray ellipsis mb-0"> Just a reminder that you have an event today </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-warning">
                      <i class="mdi mdi-cog"></i>
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject font-weight-normal mb-1">Settings</h6>
                    <p class="text-gray ellipsis mb-0"> Update dashboard </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-info">
                      <i class="mdi mdi-link-variant"></i>
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject font-weight-normal mb-1">Launch Admin</h6>
                    <p class="text-gray ellipsis mb-0"> New admin wow! </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <h6 class="p-3 mb-0 text-center">See all notifications</h6>
              </div>
            </li>
            <li class="nav-item nav-logout d-none d-lg-block">
              <a class="nav-link" href="#">
                <i class="mdi mdi-power"></i>
              </a>
            </li>
            <li class="nav-item nav-settings d-none d-lg-block">
              <a class="nav-link" href="#">
                <i class="mdi mdi-format-line-spacing"></i>
              </a>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-image">
                  <img src="assets/dashboard/assets/images/faces/face1.png" alt="profile" />
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2">{{auth()->user()->name}}</span>
                  <span class="text-secondary text-small">Project Manager</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Basic UI Elements</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-crosshairs-gps menu-icon"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/ui-features/dropdowns.html">Dropdowns</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/ui-features/typography.html">Typography</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
                <span class="menu-title">Icons</span>
                <i class="mdi mdi-contacts menu-icon"></i>
              </a>
              <div class="collapse" id="icons">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/icons/font-awesome.html">Font Awesome</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#forms" aria-expanded="false" aria-controls="forms">
                <span class="menu-title">Forms</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
              <div class="collapse" id="forms">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/forms/basic_elements.html">Form Elements</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
                <span class="menu-title">Charts</span>
                <i class="mdi mdi-chart-bar menu-icon"></i>
              </a>
              <div class="collapse" id="charts">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/charts/chartjs.html">ChartJs</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
                <span class="menu-title">Tables</span>
                <i class="mdi mdi-table-large menu-icon"></i>
              </a>
              <div class="collapse" id="tables">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/tables/basic-table.html">Basic table</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">User Pages</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-lock menu-icon"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pages/samples/blank-page.html"> Blank Page </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/samples/login.html"> Login </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/samples/register.html"> Register </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/samples/error-404.html"> 404 </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="pages/samples/error-500.html"> 500 </a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="docs/documentation.html" target="_blank">
                <span class="menu-title">Documentation</span>
                <i class="mdi mdi-file-document-box menu-icon"></i>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->
         @livewire('dashboard.⚡index')
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
       @stack('scripts')
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js">
        

    </script>

    <script src="assets/dashboard/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/dashboard/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/dashboard/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/dashboard/assets/js/off-canvas.js"></script>
    <script src="assets/dashboard/assets/js/misc.js"></script>
    <script src="assets/dashboard/assets/js/settings.js"></script>
    <script src="assets/dashboard/assets/js/todolist.js"></script>
    <script src="assets/dashboard/assets/js/jquery.cookie.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/dashboard/assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>

// ── PDF export ──────────────────────────────────────────────
function exportElementToPDF() {
    const element = document.getElementById('exportForReport');
    const options = {
        margin:      0.5,
        filename:    'customer-summary.pdf',
        image:       { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF:       { unit: 'in', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(options).from(element).save();
}

// ── Excel export — reads #crosstab directly ─────────────────
function exportTableToExcel() {
    const table = document.getElementById('crosstab');
    if (!table) { alert('Tidak ada data.'); return; }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table);

    // Auto column width
    const range = XLSX.utils.decode_range(ws['!ref']);
    const cols  = [];
    for (let c = range.s.c; c <= range.e.c; c++) {
        let max = 8;
        for (let r = range.s.r; r <= range.e.r; r++) {
            const cell = ws[XLSX.utils.encode_cell({r, c})];
            if (cell && cell.v) max = Math.max(max, String(cell.v).length + 2);
        }
        cols.push({ wch: Math.min(max, 20) });
    }
    ws['!cols'] = cols;

    const now      = new Date();
    const filename = `customer-summary-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}.xlsx`;

    XLSX.utils.book_append_sheet(wb, ws, 'Customer Summary');
    XLSX.writeFile(wb, filename);
}

// ── Google Sheets export ────────────────────────────────────
// Paste your Google Apps Script Web App URL below
const GSHEET_URL = 'https://script.google.com/macros/s/AKfycbz5p26illy3nyq-WRr89zWRIAWzlNhAJoDuOXZoowH620MwPh-F6zXDs_OxbdQLhn6xHQ/exec';

function sendToGoogleSheets() {
    const table = document.getElementById('crosstab');
    if (!table) { alert('Tidak ada data.'); return; }

    const btn = document.querySelector('[onclick="sendToGoogleSheets()"]');
    const orig = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
    btn.disabled = true;

    // Extract table data as 2D array
    const data = [];
    table.querySelectorAll('tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('th, td').forEach(cell => {
            row.push(cell.innerText.trim());
        });
        data.push(row);
    });

    fetch(GSHEET_URL, {
        method:  'POST',
        mode:    'no-cors', // Google Apps Script requires no-cors
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ data }),
    })
    .then(() => {
        btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Sent!';
        btn.classList.replace('btn-warning', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.classList.replace('btn-success', 'btn-warning');
            btn.disabled = false;
        }, 3000);
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = orig;
        btn.disabled = false;
        alert('Gagal kirim ke Google Sheets. Cek konsol untuk detail.');
    });
}
function exportOrdersTableToExcel() {
    const table = document.querySelector('.table.table-hover.align-middle');
    if (!table) { alert('Tidak ada data orders.'); return; }

    const wb = XLSX.utils.book_new();

    // Collect visible columns only (strip d-none cells)
    const rows = [];
    table.querySelectorAll('tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('th, td').forEach(cell => {
            // Skip cells hidden on desktop (d-none d-lg-table-cell hides on mobile,
            // but on export we want ALL columns — so include everything)
            row.push(cell.innerText.trim().replace(/\n+/g, ' | '));
        });
        rows.push(row);
    });

    const ws = XLSX.utils.aoa_to_sheet(rows);

    // Auto column width
    const colWidths = rows[0].map((_, colIdx) => ({
        wch: Math.min(
            Math.max(...rows.map(r => (r[colIdx] || '').length)) + 2,
            40
        )
    }));
    ws['!cols'] = colWidths;

    const now      = new Date();
    const filename = `orders-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}.xlsx`;

    XLSX.utils.book_append_sheet(wb, ws, 'Orders');
    XLSX.writeFile(wb, filename);
}
</script>
  </body>
</html>