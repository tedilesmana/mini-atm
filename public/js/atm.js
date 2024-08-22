$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// Format number with thousand separators
function formatCurrency(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Remove formatting to get the raw number value
function unformatCurrency(value) {
    return value.replace(/\./g, "");
}

// Apply formatting on input
function formatInput() {
    $(".form-control").on("input", function () {
        var value = $(this).val();
        $(this).val(formatCurrency(unformatCurrency(value)));
    });
}

function updateBalance(newBalance) {
    document.getElementById("user-balance").textContent =
        newBalance.toLocaleString("id-ID") + " IDR";
}

function validateInput(inputId, errorId, isWithdraw = false) {
    let amount = $(inputId).val();
    if (unformatCurrency(amount) === "" || unformatCurrency(amount) <= 0) {
        $(inputId).addClass("is-invalid");
        $(errorId).text("Please enter a valid amount.");
        return false;
    } else if (isWithdraw && unformatCurrency(amount) % 50000 !== 0) {
        $(inputId).addClass("is-invalid");
        $(errorId).text("Withdrawal amount must be a multiple of 50,000.");
        return false;
    } else {
        $(inputId).removeClass("is-invalid");
        $(errorId).text("");
        return true;
    }
}

function confirmDeposit() {
    if (validateInput("#deposit-amount", "#deposit-error")) {
        let amount = $("#deposit-amount").val();
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to deposit " + formatCurrency(amount) + " IDR",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, deposit it!",
        }).then((result) => {
            if (result.isConfirmed) {
                deposit(amount);
            }
        });
    }
}

function confirmWithdraw() {
    if (validateInput("#withdraw-amount", "#withdraw-error", true)) {
        let amount = $("#withdraw-amount").val();
        Swal.fire({
            title: "Are you sure?",
            text:
                "You are about to withdraw " + formatCurrency(amount) + " IDR",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, withdraw it!",
        }).then((result) => {
            if (result.isConfirmed) {
                withdraw(amount);
            }
        });
    }
}

function deposit(amount) {
    $.ajax({
        url: "/deposit",
        type: "POST",
        data: {
            amount: unformatCurrency(amount),
        },
        success: function (response) {
            getHistory();
            $("#deposit-amount").val("");
            Swal.fire(
                "Success!",
                "Deposit berhasil! Saldo Anda: " +
                    formatCurrency(response.balance),
                "success"
            );
            updateBalance(response.balance);
        },
        error: function () {
            Swal.fire("Error!", "Terjadi kesalahan!", "error");
        },
    });
}

function withdraw(amount) {
    $.ajax({
        url: "/withdraw",
        type: "POST",
        data: {
            amount: unformatCurrency(amount),
        },
        success: function (response) {
            if (response.status === "success") {
                getHistory();
                $("#withdraw-amount").val("");
                Swal.fire(
                    "Success!",
                    "Penarikan berhasil! Saldo Anda: " +
                        formatCurrency(response.balance),
                    "success"
                );
                updateBalance(response.balance);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        },
    });
}

function getHistory() {
    $.ajax({
        url: "/history",
        type: "GET",
        success: function (response) {
            let historyList = $("#transaction-history");
            historyList.empty();
            response.forEach(function (transaction) {
                var date = new Date(transaction.created_at);
                var formattedDate = date.toLocaleDateString();
                var formattedTime = date.toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                });

                historyList.append(
                    '<li class="list-group-item">' +
                        transaction.type +
                        ": " +
                        formatCurrency(transaction.amount) +
                        " - saldo terakhir: " +
                        formatCurrency(transaction.balance) +
                        ", Date: " +
                        formattedDate +
                        " " +
                        formattedTime +
                        "</li>"
                );
            });
        },
        error: function () {
            Swal.fire("Error!", "Gagal memuat riwayat transaksi!", "error");
        },
    });
}

$(document).ready(function () {
    getHistory();
    formatInput();
});
