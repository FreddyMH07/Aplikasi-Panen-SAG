/**
 * Auto Dark Mode for Sistem Panen Sawit
 * Automatically detects system preference and applies dark mode
 */

class AutoDarkMode {
    constructor() {
        this.init();
    }

    init() {
        // Initialize dark mode on page load
        this.applyInitialTheme();
        
        // Listen for system theme changes
        this.watchSystemTheme();
        
        // Apply dark mode to dynamically created elements
        this.watchDOMChanges();
    }

    applyInitialTheme() {
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('darkMode');
        
        let isDark;
        if (savedTheme !== null) {
            isDark = savedTheme === 'true';
        } else {
            isDark = systemPrefersDark;
            localStorage.setItem('darkMode', isDark);
        }
        
        this.setTheme(isDark);
    }

    setTheme(isDark) {
        document.documentElement.classList.toggle('dark', isDark);
        
        // Update any theme-dependent elements
        this.updateThemeElements(isDark);
        
        // Dispatch custom event for other components
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { isDark } 
        }));
    }

    updateThemeElements(isDark) {
        // Update meta theme-color for mobile browsers
        let themeColorMeta = document.querySelector('meta[name="theme-color"]');
        if (!themeColorMeta) {
            themeColorMeta = document.createElement('meta');
            themeColorMeta.name = 'theme-color';
            document.head.appendChild(themeColorMeta);
        }
        themeColorMeta.content = isDark ? '#111827' : '#ffffff';

        // Update any chart themes if Chart.js is present
        if (window.Chart) {
            this.updateChartThemes(isDark);
        }
    }

    updateChartThemes(isDark) {
        // Update Chart.js default colors for dark mode
        if (window.Chart && window.Chart.defaults) {
            const textColor = isDark ? '#f3f4f6' : '#374151';
            const gridColor = isDark ? '#4b5563' : '#e5e7eb';
            
            window.Chart.defaults.color = textColor;
            window.Chart.defaults.borderColor = gridColor;
            window.Chart.defaults.backgroundColor = isDark ? '#1f2937' : '#ffffff';
        }
    }

    watchSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        mediaQuery.addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            const savedTheme = localStorage.getItem('darkMode');
            if (savedTheme === null) {
                this.setTheme(e.matches);
                localStorage.setItem('darkMode', e.matches);
            }
        });
    }

    watchDOMChanges() {
        const observer = new MutationObserver((mutations) => {
            if (document.documentElement.classList.contains('dark')) {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                this.applyDarkModeToElement(node);
                            }
                        });
                    }
                });
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    applyDarkModeToElement(element) {
        // Apply dark mode styles to dynamically created elements
        if (element.classList) {
            // DataTables elements
            if (element.classList.contains('dataTables_wrapper')) {
                element.style.color = '#f3f4f6';
            }
            
            // Form elements
            if (element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                element.style.backgroundColor = '#374151';
                element.style.borderColor = '#4b5563';
                element.style.color = '#f3f4f6';
            }
            
            // Modal elements
            if (element.classList.contains('modal-content')) {
                element.style.backgroundColor = '#1f2937';
                element.style.borderColor = '#4b5563';
            }
        }
    }

    // Public method to manually toggle theme (if needed)
    toggle() {
        const isDark = !document.documentElement.classList.contains('dark');
        this.setTheme(isDark);
        localStorage.setItem('darkMode', isDark);
    }

    // Public method to get current theme
    getCurrentTheme() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.autoDarkMode = new AutoDarkMode();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AutoDarkMode;
}
