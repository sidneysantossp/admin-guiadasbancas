/**
 * Custom JavaScript para manter submenus abertos na versão desktop
 */

(function() {
    'use strict';
    
    // Função para forçar submenus abertos em desktop
    function forceOpenSubmenusOnDesktop() {
        // Verifica se é desktop (largura >= 1200px)
        if (window.innerWidth >= 1200) {
            // Seleciona todos os submenus
            const submenus = document.querySelectorAll('.js-navbar-vertical-aside-submenu');
            
            // Força todos os submenus a ficarem visíveis
            submenus.forEach(function(submenu) {
                submenu.style.display = 'block';
            });
            
            // Adiciona classe 'show' aos menus pai que têm submenus
            const menuItems = document.querySelectorAll('.navbar-vertical-aside-has-menu');
            menuItems.forEach(function(item) {
                const submenu = item.querySelector('.js-navbar-vertical-aside-submenu');
                if (submenu) {
                    item.classList.add('show');
                }
            });
            
            // Remove funcionalidade de toggle dos links principais em desktop
            const toggleLinks = document.querySelectorAll('.js-navbar-vertical-aside-menu-link.nav-link-toggle');
            toggleLinks.forEach(function(link) {
                // Remove event listeners existentes clonando o elemento
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);
                
                // Adiciona novo event listener que previne o comportamento padrão
                newLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            });
        }
    }
    
    // Função para restaurar comportamento normal em mobile
    function restoreNormalBehaviorOnMobile() {
        if (window.innerWidth < 1200) {
            // Remove forçar display dos submenus
            const submenus = document.querySelectorAll('.js-navbar-vertical-aside-submenu');
            submenus.forEach(function(submenu) {
                submenu.style.display = '';
            });
            
            // Remove classe 'show' forçada
            const menuItems = document.querySelectorAll('.navbar-vertical-aside-has-menu');
            menuItems.forEach(function(item) {
                // Só remove se não estiver ativo pela lógica normal
                if (!item.classList.contains('active')) {
                    item.classList.remove('show');
                }
            });
        }
    }
    
    // Executa quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
        forceOpenSubmenusOnDesktop();
        
        // Monitora mudanças de tamanho da tela
        window.addEventListener('resize', function() {
            // Debounce para evitar execução excessiva
            clearTimeout(window.resizeTimeout);
            window.resizeTimeout = setTimeout(function() {
                if (window.innerWidth >= 1200) {
                    forceOpenSubmenusOnDesktop();
                } else {
                    restoreNormalBehaviorOnMobile();
                }
            }, 250);
        });
    });
    
    // Também executa quando a página estiver totalmente carregada
    window.addEventListener('load', function() {
        // Pequeno delay para garantir que todos os scripts tenham carregado
        setTimeout(forceOpenSubmenusOnDesktop, 100);
    });
    
})();