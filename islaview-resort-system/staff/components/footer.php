<footer class="text-center py-4 text-muted small">
        &copy; <?php echo date('Y'); ?> IslaView Resort - Staff Management Portal
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live Clock Script
        function updateClock() {
            const clockElement = document.getElementById('digitalClock');
            if(clockElement) {
                const now = new Date();
                const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
                clockElement.innerHTML = timeStr;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>