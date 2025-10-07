    // Enhanced Alert and Notification System
class AlertSystem {
    constructor() {
        this.createToastContainer();
        this.initializeAlerts();
    }

    // Create toast container if it doesn't exist
    createToastContainer() {
        if (!document.querySelector('.toast-container')) {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
    }

    // Initialize existing alerts with close functionality
    initializeAlerts() {
        document.querySelectorAll('.alert').forEach(alert => {
            this.makeAlertDismissible(alert);
        });
    }

    // Make alert dismissible
    makeAlertDismissible(alert) {
        if (!alert.querySelector('.alert-close')) {
            alert.classList.add('alert-dismissible');
            const closeBtn = document.createElement('button');
            closeBtn.className = 'alert-close';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = () => this.dismissAlert(alert);
            alert.appendChild(closeBtn);
        }
    }

    // Dismiss alert with animation
    dismissAlert(alert) {
        alert.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }

    // Show toast notification
    showToast(message, type = 'info', title = '', duration = 5000) {
        const container = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };

        toast.innerHTML = `
            <div class="toast-icon" style="color: ${colors[type]}">
                <i class="${icons[type]}"></i>
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">&times;</button>
        `;

        // Add close functionality
        toast.querySelector('.toast-close').onclick = () => {
            this.dismissToast(toast);
        };

        container.appendChild(toast);

        // Auto dismiss after duration
        if (duration > 0) {
            setTimeout(() => {
                this.dismissToast(toast);
            }, duration);
        }

        return toast;
    }

    // Dismiss toast
    dismissToast(toast) {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    // Show confirmation dialog
    showConfirm(options = {}) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            type = 'warning',
            onConfirm = () => { },
            onCancel = () => { }
        } = options;

        const icons = {
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-exclamation-circle',
            info: 'fas fa-question-circle'
        };

        const colors = {
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8'
        };

        const modal = this.createModal(`
            <div class="confirm-dialog">
                <div class="modal-body">
                    <div class="confirm-icon" style="color: ${colors[type]}">
                        <i class="${icons[type]}"></i>
                    </div>
                    <div class="confirm-title">${title}</div>
                    <div class="confirm-message">${message}</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary cancel-btn">${cancelText}</button>
                    <button class="btn btn-primary confirm-btn">${confirmText}</button>
                </div>
            </div>
        `);

        // Add event listeners
        modal.querySelector('.cancel-btn').onclick = () => {
            this.closeModal(modal);
            onCancel();
        };

        modal.querySelector('.confirm-btn').onclick = () => {
            this.closeModal(modal);
            onConfirm();
        };

        this.showModal(modal);
        return modal;
    }

    // Create modal
    createModal(content) {
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';

        overlay.innerHTML = `
            <div class="modal">
                ${content}
            </div>
        `;

        // Close on overlay click
        overlay.onclick = (e) => {
            if (e.target === overlay) {
                this.closeModal(overlay);
            }
        };

        // Close on escape key
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                this.closeModal(overlay);
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);

        document.body.appendChild(overlay);
        return overlay;
    }

    // Show modal
    showModal(modal) {
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);
    }

    // Close modal
    closeModal(modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            if (modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 300);
    }

    // Show loading overlay
    showLoading(message = 'Loading...') {
        const modal = this.createModal(`
            <div class="modal-body" style="text-align: center; padding: 3rem;">
                <div class="spinner spinner-lg" style="margin-bottom: 1rem;"></div>
                <div style="color: #666;">${message}</div>
            </div>
        `);

        this.showModal(modal);
        return modal;
    }

    // Show success message
    success(message, title = 'Success!') {
        return this.showToast(message, 'success', title);
    }

    // Show error message
    error(message, title = 'Error!') {
        return this.showToast(message, 'error', title);
    }

    // Show warning message
    warning(message, title = 'Warning!') {
        return this.showToast(message, 'warning', title);
    }

    // Show info message
    info(message, title = 'Info') {
        return this.showToast(message, 'info', title);
    }
}

// Initialize alert system
const alerts = new AlertSystem();

// Global functions for easy access
window.showToast = (message, type, title, duration) => alerts.showToast(message, type, title, duration);
window.showConfirm = (options) => alerts.showConfirm(options);
window.showLoading = (message) => alerts.showLoading(message);
window.closeModal = (modal) => alerts.closeModal(modal);

// Enhanced form validation
class FormValidator {
    constructor(form) {
        this.form = form;
        this.rules = {};
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validate()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        this.form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    addRule(fieldName, validator, message) {
        if (!this.rules[fieldName]) {
            this.rules[fieldName] = [];
        }
        this.rules[fieldName].push({ validator, message });
        return this;
    }

    validate() {
        let isValid = true;

        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const fieldName = field.name;
        const rules = this.rules[fieldName] || [];

        this.clearFieldError(field);

        for (let rule of rules) {
            if (!rule.validator(field.value, field)) {
                this.showFieldError(field, rule.message);
                return false;
            }
        }

        this.showFieldSuccess(field);
        return true;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    showFieldSuccess(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');

        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid', 'is-valid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
}

// Common validation rules
const ValidationRules = {
    required: (value) => value.trim() !== '',
    email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    minLength: (min) => (value) => value.length >= min,
    maxLength: (max) => (value) => value.length <= max,
    pattern: (regex) => (value) => regex.test(value),
    match: (otherFieldName) => (value, field) => {
        const otherField = field.form.querySelector(`[name="${otherFieldName}"]`);
        return otherField && value === otherField.value;
    },
    strongPassword: (value) => {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }
};

// Auto-dismiss alerts after 10 seconds
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
            alerts.dismissAlert(alert);
        });
    }, 10000);
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AlertSystem, FormValidator, ValidationRules };
}