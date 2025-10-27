// js/script.js
class AdminSystem {
    constructor() {
        this.init();
    }

    init() {
        this.initModals();
        this.initSearch();
        this.initForms();
    }

    initModals() {
        // Fechar modais
        document.querySelectorAll('.close, .cancel-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) {
                    this.closeModal(modal);
                }
            });
        });

        // Fechar modal ao clicar fora
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal);
                }
            });
        });
    }

    initSearch() {
        const searchInputs = document.querySelectorAll('.search-box input');
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const table = e.target.closest('.card').querySelector('tbody');
                const rows = table.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });
    }

    initForms() {
        // Validação de formulários
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const required = form.querySelectorAll('[required]');
        
        required.forEach(field => {
            if (!field.value.trim()) {
                this.showError(field, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearError(field);
            }
        });

        // Validação de email
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showError(field, 'Email inválido');
                isValid = false;
            }
        });

        return isValid;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    showError(field, message) {
        this.clearError(field);
        field.style.borderColor = '#e74c3c';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    clearError(field) {
        field.style.borderColor = '';
        const error = field.parentNode.querySelector('.field-error');
        if (error) {
            error.remove();
        }
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Limpar formulários no modal
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }

    confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            min-width: 300px;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }


    // Validação de CPF
    isValidCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
        
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;
        
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(10))) return false;
        
        return true;
    }

    // Formatar CPF
    formatCPF(cpf) {
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    // Validação de formulário de administrador
    validateAdminForm(form) {
        let isValid = true;
        
        // Validação básica de campos obrigatórios
        const required = form.querySelectorAll('[required]');
        required.forEach(field => {
            if (!field.value.trim()) {
                this.showError(field, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearError(field);
            }
        });

        // Validação de email
        const emailField = form.querySelector('input[type="email"]');
        if (emailField && emailField.value && !this.isValidEmail(emailField.value)) {
            this.showError(emailField, 'Email inválido');
            isValid = false;
        }

        // Validação de CPF
        const cpfField = form.querySelector('input[name="cpf"]');
        if (cpfField && cpfField.value) {
            if (!this.isValidCPF(cpfField.value)) {
                this.showError(cpfField, 'CPF inválido');
                isValid = false;
            } else {
                // Formatar CPF automaticamente
                cpfField.value = this.formatCPF(cpfField.value.replace(/\D/g, ''));
            }
        }

        // Validação de senha (para formulário de adicionar/alterar senha)
        const senhaField = form.querySelector('input[name="senha"], input[name="nova_senha"]');
        if (senhaField && senhaField.value && senhaField.value.length < 6) {
            this.showError(senhaField, 'A senha deve ter pelo menos 6 caracteres');
            isValid = false;
        }

        return isValid;
    }


}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.adminSystem = new AdminSystem();
});

// Funções utilitárias
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}