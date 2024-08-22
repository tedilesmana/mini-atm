@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>ATM System</h1>

    <!-- Display User Balance -->
    <div class="alert alert-info" role="alert">
        <strong>Saldo Anda: </strong> <span id="user-balance">{{ number_format($balance, 0, ',', '.') }} IDR</span>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Deposit</h4>
            <input type="text" id="deposit-amount" class="form-control" placeholder="Enter amount" />
            <small id="deposit-error" class="text-danger"></small>
            <br>
            <button onclick="confirmDeposit()" class="btn btn-success mt-2">Deposit</button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Withdraw</h4>
            <input type="text" id="withdraw-amount" class="form-control" placeholder="Enter amount" />
            <small id="withdraw-error" class="text-danger"></small>
            <br>
            <button onclick="confirmWithdraw()" class="btn btn-danger mt-2">Withdraw</button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Transaction History</h4>
            <ul id="transaction-history" class="list-group mt-2"></ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/atm.js') }}"></script>
@endsection
