<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Lawyer Booking' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .step-active {
            @apply bg-blue-600 text-white;
        }
        .step-completed {
            @apply bg-green-500 text-white;
        }
        .lawyer-card:hover {
            @apply border-blue-500 shadow-lg;
        }
        .lawyer-card.selected {
            @apply border-blue-500 bg-blue-50;
        }
        .time-slot:hover {
            @apply bg-blue-100;
        }
        .time-slot.selected {
            @apply bg-blue-500 text-white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-blue-600" href="index.php">
                <i class="fas fa-balance-scale me-2"></i>LegalConnect
            </a>
        </div>
    </nav>