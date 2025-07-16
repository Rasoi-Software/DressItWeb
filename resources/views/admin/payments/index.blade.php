@extends('layouts.admin')

@section('content')
<!-- Your page content here -->
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                        <h6 class="text-white text-capitalize mb-0">Payment List</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table table-bordered table-striped align-items-center mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">#</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">User</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Amount</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Currency</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Payment ID</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $index => $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div>
                                                <img src="{{$payment->user->profile_image}}" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $payment->user->name ?? 'N/A' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $payment->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">₹{{ number_format($payment->amount / 100, 2) }}</td>
                                    <td class="text-center">{{ strtoupper($payment->currency) }}</td>
                                    <td class="text-center">
                                        @if($payment->status === 'succeeded')
                                        <span class="badge bg-gradient-success">{{ $payment->status }}</span>
                                        @else
                                        <span class="badge bg-gradient-warning text-dark">{{ $payment->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-xs">{{ $payment->payment_intent_id }}</td>
                                    <td class="text-center text-xs">{{ $payment->created_at->format('d M, Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')

<script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["M", "T", "W", "T", "F", "S", "S"],
            datasets: [{
                label: "Views",
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                backgroundColor: "#43A047",
                data: [50, 45, 22, 28, 50, 60, 76],
                barThickness: 'flex'
            }, ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: '#e5e5e5'
                    },
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 500,
                        beginAtZero: true,
                        padding: 10,
                        font: {
                            size: 14,
                            lineHeight: 2
                        },
                        color: "#737373"
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#737373',
                        padding: 10,
                        font: {
                            size: 14,
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
        type: "line",
        data: {
            labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
            datasets: [{
                label: "Sales",
                tension: 0,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: "#43A047",
                pointBorderColor: "transparent",
                borderColor: "#43A047",
                backgroundColor: "transparent",
                fill: true,
                data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const fullMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                            return fullMonths[context[0].dataIndex];
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [4, 4],
                        color: '#e5e5e5'
                    },
                    ticks: {
                        display: true,
                        color: '#737373',
                        padding: 10,
                        font: {
                            size: 12,
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#737373',
                        padding: 10,
                        font: {
                            size: 12,
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Tasks",
                tension: 0,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: "#43A047",
                pointBorderColor: "transparent",
                borderColor: "#43A047",
                backgroundColor: "transparent",
                fill: true,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [4, 4],
                        color: '#e5e5e5'
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#737373',
                        font: {
                            size: 14,
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [4, 4]
                    },
                    ticks: {
                        display: true,
                        color: '#737373',
                        padding: 10,
                        font: {
                            size: 14,
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>

@endpush