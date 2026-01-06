<!-- Sidebar Partial -->
<nav class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/Logo.jpg') }}" alt="SignUpGo Logo">
        <span>SignUpGo</span>
    </div>
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                📊 <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') && !request()->routeIs('registrations.*') ? 'active' : '' }}">
                📝 <span>Events</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('registrations.index') }}" class="nav-link {{ request()->routeIs('registrations.*') ? 'active' : '' }}">
                ⭐ <span>My Registrations</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('feedback.index') }}" class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                💬 <span>Event Feedback</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('account.index') }}" class="nav-link {{ request()->routeIs('account.*') ? 'active' : '' }}">
                👤 <span>Account</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('certificates.index') }}" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                🎓 <span>My Certificates</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mailbox.index') }}" class="nav-link {{ request()->routeIs('mailbox.*') ? 'active' : '' }}">
                📧 <span>Mail box</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a href="#" class="nav-link">
                ⚙️ <span>Settings</span>
            </a>
        </li> --}}
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin: 0;">
                @csrf
                <a href="#" class="nav-link" style="background: #e74c3c; color: white; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">
                    🚪 <span>Logout</span>
                </a>
            </form>
        </li>
    </ul>
</nav>
