<nav class="mt-2 mb-4">
    <ul class="nav nav-pills nav-sidebar flex-column mb-4" data-widget="treeview" role="menu" data-accordion="false">
           
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- <form action="{{ route('toggle.sidebar') }}" method="POST">
            @csrf
            <input type="hidden" name="sidebar" value="1">
            <button type="submit" class="btn btn-info my-2">
                Switch to Business <i class="fas fa-arrow-right"></i>
            </button>
        </form> --}}

        <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2">
            Switch to Business <i class="fas fa-arrow-right"></i>
        </a>

        <li class="nav-item">
            <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ (request()->is('admin/chart-of-account*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Chart Of Accounts</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.income') }}" class="nav-link {{ (request()->is('admin/income')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Income</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.expense') }}" class="nav-link {{ (request()->is('admin/expense*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Expense</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.asset') }}" class="nav-link {{ (request()->is('admin/asset*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Assets</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.liabilities') }}" class="nav-link {{ (request()->is('admin/liabilities*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Liabilities</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.equity') }}" class="nav-link {{ (request()->is('admin/equity*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Equity</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.equityholders') }}" class="nav-link {{ (request()->is('admin/share-holders*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Equity Holder</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.ledgeraccount') }}" class="nav-link {{ (request()->is('admin/ledger-accounts*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Ledger</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('cashflow') }}" class="nav-link {{ (request()->is('admin/cashflow*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Cash flow</p>
            </a>
        </li>

        <li class="nav-item dropdown {{ request()->is('admin/cash-book') || request()->is('admin/bank-book')  ? 'menu-open' : '' }}">

            <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/cash-book') || request()->is('admin/bank-book') ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Day Book <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('bankbook') }}" class="nav-link {{ request()->routeIs('bankbook') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Bank Book</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cashbook') }}" class="nav-link {{ request()->routeIs('cashbook') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Cash Book</p>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item dropdown {{ request()->is('admin/income-statement') || request()->is('admin/balance-sheet')  ? 'menu-open' : '' }}">

            <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/income-statement') || request()->is('admin/balance-sheet') ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Financial Statement <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.incomestatement') }}" class="nav-link {{ request()->routeIs('admin.incomestatement') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Income Statement</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.balancesheet') }}" class="nav-link {{ request()->routeIs('admin.balancesheet') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Balance Sheet</p>
                    </a>
                </li>
            </ul>
        </li>


        <li class="mb-5"></li>
        <li class="mb-5"></li>
        <li class="mb-5"></li>
        {{--  
        <li class="nav-item">
            <a href="{{ route('view_branch') }}" class="nav-link {{ (request()->is('admin/branch*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Branch</p>
            </a>
        </li>
        --}}
        
    </ul>
  </nav>