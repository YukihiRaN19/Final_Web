<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "ระบบเย็บผ้า"; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
        .sidebar {
            flex: 0 0 250px;
            background-color: #ffffff;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            border: 2px solid #3b82f6; 
            height: fit-content;
        }
        .sidebar a {
            display: block; 
            padding: 0.75rem 1rem;
            text-decoration: none;
            font-size: 1rem;
            color: #333;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #e0f2fe;
            color: #0ea5e9;
        }
        .menu-title {
            padding: 0 1rem 0.75rem 1rem;
            margin: 0 0 0.75rem 0;
            font-size: 1.1rem; 
            font-weight: 700; 
            color: #212529; 
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="bg-light">