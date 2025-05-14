document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (sidebar.classList.contains('active') && 
                !event.target.closest('.sidebar') && 
                !event.target.closest('.sidebar-toggle')) {
                sidebar.classList.remove('active');
            }
        });
    }
    
    // Notifications dropdown
    const notificationsBtn = document.querySelector('.notifications-btn');
    const notificationsDropdown = document.querySelector('.notifications-dropdown');
    
    if (notificationsBtn && notificationsDropdown) {
        notificationsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsDropdown.style.display = notificationsDropdown.style.display === 'block' ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (notificationsDropdown.style.display === 'block' && 
                !event.target.closest('.notifications-dropdown') && 
                !event.target.closest('.notifications-btn')) {
                notificationsDropdown.style.display = 'none';
            }
        });
        
        // Mark notification as read
        const notificationReadBtns = document.querySelectorAll('.notification-read-btn');
        
        notificationReadBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const notificationItem = this.closest('.notification-item');
                notificationItem.classList.remove('unread');
                
                const icon = this.querySelector('i');
                icon.classList.remove('fas');
                icon.classList.add('far');
                
                // Update notification count
                const badgeCount = parseInt(document.querySelector('.notification-badge').textContent);
                if (badgeCount > 0) {
                    document.querySelector('.notification-badge').textContent = badgeCount - 1;
                }
            });
        });
    }
    
    // Charts
    if (typeof Chart !== 'undefined') {
        // Weight Progress Chart
        const weightCtx = document.getElementById('weightChart');
        
        if (weightCtx) {
            const weightData = {
                labels: [
                    'Sep 15', 'Sep 20', 'Sep 25', 'Sep 30', 
                    'Oct 5', 'Oct 10', 'Oct 15'
                ],
                datasets: [{
                    label: 'Weight (lbs)',
                    data: [159, 158, 156.5, 155.8, 154.9, 154.2, 154],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4CAF50',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            };
            
            new Chart(weightCtx, {
                type: 'line',
                data: weightData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            min: 150,
                            max: 160,
                            ticks: {
                                stepSize: 2
                            }
                        }
                    }
                }
            });
        }
        
        // Macronutrients Chart
        const macrosCtx = document.getElementById('macrosChart');
        
        if (macrosCtx) {
            const macrosData = {
                labels: ['Protein', 'Carbs', 'Fats'],
                datasets: [{
                    data: [85, 140, 36],
                    backgroundColor: ['#4CAF50', '#2196F3', '#FF9800'],
                    borderWidth: 0
                }]
            };
            
            new Chart(macrosCtx, {
                type: 'doughnut',
                data: macrosData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Calories Chart
        const caloriesCtx = document.getElementById('caloriesChart');
        
        if (caloriesCtx) {
            const caloriesData = {
                labels: ['Consumed', 'Remaining'],
                datasets: [{
                    data: [1620, 380],
                    backgroundColor: ['#2196F3', '#E0E0E0'],
                    borderWidth: 0
                }]
            };
            
            new Chart(caloriesCtx, {
                type: 'doughnut',
                data: caloriesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }
});