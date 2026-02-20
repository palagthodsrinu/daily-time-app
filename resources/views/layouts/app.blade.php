<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BBIS Timesheet')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Your existing CSS styles remain exactly the same */
        :root {
            --primary: #3b82f6;
            --primary-light: #eff6ff;
            --primary-dark: #1d4ed8;
            --secondary: #10b981;
            --light: #f8fafc;
            --white: #ffffff;
            --text: #334155;
            --text-light: #64748b;
            --border: #e2e8f0;
        }

        body {
            background: var(--light) !important;
            color: var(--text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px; /* Account for fixed navbar */
        }

        .navbar {
            background: var(--white) !important;
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 80px;
        }

        .sidebar {
            background: var(--white);
            border-right: 1px solid var(--border);
            transition: all 0.3s;
            box-shadow: 2px 0 4px rgba(0,0,0,0.05);
            position: fixed;
            top: 80px; /* Below navbar */
            left: 0;
            bottom: 0;
            width: 280px;
            overflow-y: auto;
            z-index: 1020;
        }

        .sidebar .nav-link {
            color: var(--text-light);
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 0.8rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 280px; /* Account for sidebar */
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }

        .dashboard-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            color: var(--text);
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
            border-color: var(--primary);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: var(--primary-light);
            color: var(--primary);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .chart-container {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table-container {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--primary-light);
            border-bottom: 1px solid var(--border);
            padding: 1rem;
            font-weight: 600;
            color: var(--text);
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background: var(--primary-light);
        }

        .badge {
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-actions .btn {
            background: var(--white);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            padding: 0.8rem 1.2rem;
            margin: 0.3rem;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .quick-actions .btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        }

        .search-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            color: var(--text);
        }

        .search-box:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .navbar-brand {
            color: var(--primary) !important;
            font-weight: 700;
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-sidebar-toggle {
                display: block !important;
            }
        }

        .mobile-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Include Header -->
    @include('layouts.header')
    
    <!-- Include Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.mobile-sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }

            // Initialize Charts if needed
            if (document.getElementById('hoursChart')) {
                const hoursCtx = document.getElementById('hoursChart').getContext('2d');
                const hoursChart = new Chart(hoursCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [
                            {
                                label: 'Billable Hours',
                                data: [32, 28, 35, 40, 38, 15, 8],
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            },
                            {
                                label: 'Non-Billable Hours',
                                data: [8, 12, 5, 10, 12, 5, 2],
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>