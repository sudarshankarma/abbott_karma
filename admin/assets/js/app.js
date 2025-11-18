// Application JavaScript - Complete Version

class AdminApp {
    constructor() {
        this.currentDocument = {};
        this.init();
    }

    init() {
        this.initializeComponents();
        this.setupEventListeners();
        this.startSessionTimer();
    }

    initializeComponents() {
        // Initialize Materialize components
        this.initializeMaterialize();
        
        // Initialize DataTables if present
        this.initializeDataTables();
        
        // Initialize any custom components
        this.initializeCustomComponents();
    }

    initializeMaterialize() {
        // Sidenav initialization
        const sidenavs = document.querySelectorAll('.sidenav');
        M.Sidenav.init(sidenavs);

        // Dropdown initialization
        const dropdowns = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdowns, { 
            coverTrigger: false,
            constrainWidth: false
        });

        // Modal initialization
        const modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);

        // Select initialization
        const selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);

        // Tooltip initialization
        const tooltips = document.querySelectorAll('.tooltipped');
        M.Tooltip.init(tooltips);

        // Tabs initialization
        const tabs = document.querySelectorAll('.tabs');
        M.Tabs.init(tabs);

        // Collapsible initialization
        const collapsibles = document.querySelectorAll('.collapsible');
        M.Collapsible.init(collapsibles);

        // Character counter initialization
        const characterCounters = document.querySelectorAll('.character-counter');
        M.CharacterCounter.init(characterCounters);
    }

    initializeDataTables() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('.datatable').DataTable({
                "pageLength": 25,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "dom": '<"row"<"col s6"l><"col s6"f>>rt<"row"<"col s6"i><"col s6"p>>',
                "initComplete": function(settings, json) {
                    $('.dataTables_filter input[type="search"]').addClass('browser-default');
                }
            });
        }
    }

    initializeCustomComponents() {
        // Auto-dismiss alerts after 5 seconds
        this.autoDismissAlerts();
        
        // Initialize any other custom components
        this.initializeDocumentPreview();
    }

    setupEventListeners() {
        // Session timeout warning
        this.setupSessionTimer();
        
        // Form submission handlers
        this.setupFormHandlers();
        
        // Document preview handlers
        this.setupDocumentHandlers();
    }

    setupSessionTimer() {
        let warningTimer;
        
        const startSessionTimer = () => {
            warningTimer = setTimeout(() => {
                M.toast({
                    html: 'Your session will expire in 5 minutes due to inactivity.',
                    classes: 'orange',
                    displayLength: 10000
                });
            }, 3300000); // 55 minutes
        };

        const resetSessionTimer = () => {
            clearTimeout(warningTimer);
            startSessionTimer();
        };

        // Reset timer on user activity
        document.addEventListener('mousemove', resetSessionTimer);
        document.addEventListener('keypress', resetSessionTimer);
        document.addEventListener('click', resetSessionTimer);
        document.addEventListener('scroll', resetSessionTimer);

        startSessionTimer();
    }

    setupFormHandlers() {
        // Generic form submission handler
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i> Processing...';
                
                // Re-enable button after 10 seconds in case of error
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });

        // Search form handlers
        document.addEventListener('keypress', function(e) {
            if (e.target.matches('#search') && e.key === 'Enter') {
                e.preventDefault();
                const applyFiltersBtn = document.querySelector('button[onclick*="applyFilters"]');
                if (applyFiltersBtn) applyFiltersBtn.click();
            }
        });
    }

    setupDocumentHandlers() {
        // Global document preview function
        window.previewDocument = this.previewDocument.bind(this);
        
        // Global document status update functions
        window.approveCurrentDocument = this.approveCurrentDocument.bind(this);
        window.rejectCurrentDocument = this.rejectCurrentDocument.bind(this);
        window.pendingCurrentDocument = this.pendingCurrentDocument.bind(this);
        window.updateDocumentStatus = this.updateDocumentStatus.bind(this);
    }

    previewDocumentOld(appId, applicantName, docType, filePath, currentStatus, submittedDate) {
        this.currentDocument = {
            appId: appId,
            applicantName: applicantName,
            docType: docType,
            filePath: filePath,
            currentStatus: currentStatus,
            submittedDate: submittedDate
        };

        // Update modal information
        document.getElementById('modalTitle').textContent = `${docType.toUpperCase()} Document - ${appId}`;
        document.getElementById('infoAppId').textContent = appId;
        document.getElementById('infoApplicant').textContent = applicantName;
        document.getElementById('infoDocType').textContent = docType.toUpperCase();
        document.getElementById('infoSubmitted').textContent = submittedDate;
        
        // Update status badge
        const statusElement = document.getElementById('infoStatus');
        statusElement.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
        statusElement.className = `badge ${currentStatus === 'approved' ? 'green' : currentStatus === 'rejected' ? 'red' : 'orange'} white-text`;
        
        // Set download link
        const downloadBtn = document.getElementById('downloadBtn');
        if (downloadBtn) {
            downloadBtn.href = filePath;
            downloadBtn.download = `${appId}_${docType}`;
        }

        // Show loading, hide other content
        this.showDocumentLoading();

        // Open modal
        const modalElement = document.getElementById('documentModal');
        if (modalElement) {
            const modal = M.Modal.getInstance(modalElement);
            modal.open();
        }

        // Load document
        this.loadDocumentPreview(filePath);
    }

    previewDocument(appId, applicantName, docType, filePath, currentStatus, submittedDate) {
         // Construct the correct file path
        const fullFilePath = `../uploads/${appId}/${filePath}`;
        
        this.currentDocument = {
            appId: appId,
            applicantName: applicantName,
            docType: docType,
            filePath: fullFilePath,
            currentStatus: currentStatus,
            submittedDate: submittedDate
        };

        // Update modal information
        document.getElementById('modalTitle').textContent = `${docType.toUpperCase()} Document - ${appId}`;
        document.getElementById('infoAppId').textContent = appId;
        document.getElementById('infoApplicant').textContent = applicantName;
        document.getElementById('infoDocType').textContent = docType.toUpperCase();
        document.getElementById('infoSubmitted').textContent = submittedDate;
        
        // Update status badge
        const statusElement = document.getElementById('infoStatus');
        statusElement.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
        statusElement.className = `badge ${currentStatus === 'approved' ? 'green' : currentStatus === 'rejected' ? 'red' : 'orange'} white-text`;
        
        // Set download link
        const downloadBtn = document.getElementById('downloadBtn');
        if (downloadBtn) {
            downloadBtn.href = fullFilePath;
            downloadBtn.download = `${appId}_${docType}`;
        }

        // Show loading, hide other content
        this.showDocumentLoading();

        // Open modal
        const modalElement = document.getElementById('documentModal');
        if (modalElement) {
            const modal = M.Modal.getInstance(modalElement);
            modal.open();
        }

        // Load document
        this.loadDocumentPreview(fullFilePath);
    }

  
    showDocumentLoading() {
        const loading = document.getElementById('documentLoading');
        const image = document.getElementById('documentImage');
        const pdf = document.getElementById('documentPdf');
        const error = document.getElementById('documentError');

        if (loading) loading.style.display = 'block';
        if (image) image.style.display = 'none';
        if (pdf) pdf.style.display = 'none';
        if (error) error.style.display = 'none';
    }

    loadDocumentPreview(filePath) {
        if (!filePath || filePath === 'null') {
            this.showDocumentError();
            return;
        }

        if (filePath.toLowerCase().endsWith('.pdf')) {
            this.loadPdfDocument(filePath);
        } else {
            this.loadImageDocument(filePath);
        }
    }

    loadPdfDocument(filePath) {
        setTimeout(() => {
            const loading = document.getElementById('documentLoading');
            const pdf = document.getElementById('documentPdf');
            
            if (loading) loading.style.display = 'none';
            if (pdf) {
                pdf.style.display = 'block';
                pdf.src = filePath;
            }
        }, 1000);
    }

    loadImageDocument(filePath) {
        const img = new Image();
        const loading = document.getElementById('documentLoading');
        const image = document.getElementById('documentImage');
        const error = document.getElementById('documentError');
         const fullFilePath = `../uploads/${appId}/${filePath}`;

        img.onload = () => {
            if (loading) loading.style.display = 'none';
            if (image) {
                image.style.display = 'block';
                image.src = fullFilePath;
            }
        };

        img.onerror = () => {
            if (loading) loading.style.display = 'none';
            if (error) error.style.display = 'block';
        };

        img.src = fullFilePath;
    }

    showDocumentError() {
        const loading = document.getElementById('documentLoading');
        const error = document.getElementById('documentError');

        if (loading) loading.style.display = 'none';
        if (error) error.style.display = 'block';
    }

    approveCurrentDocument() {
        this.updateDocumentStatus(this.currentDocument.appId, this.currentDocument.docType, 'approved');
    }

    rejectCurrentDocument() {
        this.updateDocumentStatus(this.currentDocument.appId, this.currentDocument.docType, 'rejected');
    }

    pendingCurrentDocument() {
        this.updateDocumentStatus(this.currentDocument.appId, this.currentDocument.docType, 'pending');
    }

    updateDocumentStatus(appId, docType, status) {
        if (!confirm(`Are you sure you want to mark this ${docType} document as ${status}?`)) return;

        $.post('?controller=applications&action=updateDocument', {
            application_id: appId,
            document_type: docType,
            status: status
        })
        .done((response) => {
            const result = typeof response === 'string' ? JSON.parse(response) : response;
            if (result.success) {
                M.toast({html: 'Document status updated successfully!', classes: 'green'});
                this.closeDocumentModalAndReload();
            } else {
                M.toast({html: 'Error: ' + result.message, classes: 'red'});
            }
        })
        .fail((xhr, status, error) => {
            M.toast({html: 'Network error: ' + error, classes: 'red'});
        });
    }

    closeDocumentModalAndReload() {
        const modalElement = document.getElementById('documentModal');
        if (modalElement) {
            const modal = M.Modal.getInstance(modalElement);
            modal.close();
        }
        setTimeout(() => location.reload(), 1000);
    }

    autoDismissAlerts() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.card-panel');
            alerts.forEach(alert => {
                if (alert.classList.contains('green-lighten-4') || 
                    alert.classList.contains('red-lighten-4') || 
                    alert.classList.contains('orange-lighten-4')) {
                    alert.style.display = 'none';
                }
            });
        }, 5000);
    }

    startSessionTimer() {
        // Additional session management can be added here
        console.log('Admin application initialized');
    }

    // Utility functions
    showNotification(message, type = 'info') {
        const classes = {
            'success': 'green',
            'error': 'red',
            'info': 'blue',
            'warning': 'orange'
        }[type] || 'blue';

        M.toast({html: message, classes: classes});
    }

    confirmAction(message) {
        return confirm(message);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Global utility functions
window.applyFilters = function() {
    const search = document.getElementById('search')?.value || '';
    const status = document.getElementById('status_filter')?.value || '';
    
    let url = window.location.href.split('?')[0];
    const params = [];
    
    if (search) params.push(`search=${encodeURIComponent(search)}`);
    if (status) params.push(`status=${encodeURIComponent(status)}`);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
};

window.clearFilters = function() {
    window.location.href = window.location.href.split('?')[0];
};

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.adminApp = new AdminApp();
});

// Export for module usage (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminApp;
}