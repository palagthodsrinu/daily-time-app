<!-- Sidebar -->
<div class="sidebar d-none d-md-block">
    <div class="d-flex flex-column flex-shrink-0 p-3 h-100">

        <ul class="nav nav-pills flex-column mb-auto mt-3">

            {{-- Dashboard – visible to ALL --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            {{-- ===== ADMIN ONLY ===== --}}
            @if(auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" 
                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clients.index') }}" 
                    class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.index') }}" 
                    class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('assignments.index') }}" 
                       class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Assign Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" 
                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
            @endif
            {{-- ===== END ADMIN ===== --}}

            {{-- ===== SUPERVISOR ONLY ===== --}}
            @if(auth()->user()->hasRole('supervisor'))
                <li class="nav-item">
                    <a href="{{ route('supervisor.timesheets.index') }}" 
                       class="nav-link {{ request()->routeIs('supervisor.timesheets.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i>Review Timesheets
                    </a>
                </li>

                <!-- <li class="nav-item">
                    <a href="{{ route('reports.index') }}" 
                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li> -->
            @endif
            {{-- ===== END SUPERVISOR ===== --}}

            {{-- ===== PERSONAL TIMESHEETS ===== --}}
            @if(auth()->user()->hasRole('employee') || auth()->user()->hasRole('supervisor'))
            <li class="nav-item">
                @if(auth()->user()->hasRole('supervisor'))
                    {{-- Supervisors use employee timesheets for their own entries --}}
                    <a href="{{ route('timesheets.index') }}" 
                       class="nav-link position-relative {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> My Timesheets
                        @if($pendingTimesheets ?? 0 > 0)
                            <span class="notification-badge">{{ $pendingTimesheets ?? 0 }}</span>
                        @endif
                    </a>
                @else
                    {{-- Employees use employee timesheets --}}
                    <a href="{{ route('timesheets.index') }}" 
                    class="nav-link position-relative {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> My Timesheets
                        @if($pendingTimesheets ?? 0 > 0)
                            <span class="notification-badge">{{ $pendingTimesheets ?? 0 }}</span>
                        @endif
                    </a>
                @endif
            </li>
            @endif

            {{-- Settings – visible to ALL roles --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>

        </ul>

        <!-- Footer -->
        <div class="mt-auto p-3 border-top">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3">
                    <span>{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <small class="fw-bold">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </small>
                    <br>
                    <small class="text-muted">
                        {{ ucfirst(auth()->user()->roles->pluck('name')->implode(', ')) }}
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>