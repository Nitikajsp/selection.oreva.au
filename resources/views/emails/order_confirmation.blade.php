<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            margin-top: 110px;
            margin-right: 20px;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        @page: first {
            margin: 0;
        }

        @page :first {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #F9F9F9;
            margin: 0;
            padding: 0;
        }

        .page-bg {
            position: fixed;
            top: -200px;
            left: -200px;
            right: -200px;
            bottom: -200px;
            background-color: #F9F9F9;
            z-index: -1;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        .invoice-box {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            border: none;
            background-color: #F9F9F9;
            box-shadow: none;
            border-radius: 0;
            box-sizing: border-box;
            position: relative;
        }

        .invoice-header-wrapper {
            margin-bottom: 16px;
            padding: 3px;
        }

        .invoice-header-box {
            background-color: #F9F9F9;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            display: block;
            position: relative;
            overflow: visible;
            padding: 20px 25px;
        }

        td {
            background-color: #F9F9F9;
        }

        .invoice-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-header-left {
            font-size: 14px;
            line-height: 1.8;
            padding-right: 80px;
            vertical-align: top;
        }

        .invoice-header-right {
            font-size: 14px;
            line-height: 1.8;
            text-align: right;
            vertical-align: top;
        }

        .pdf-header-logo {
            position: fixed;
            top: -90px;
            right: 0px;
            text-align: right;
            z-index: 9999;
        }

        .static-header-logo {
            position: absolute;
            top: -90px;
            right: 0px;
            text-align: right;
            z-index: 9998;
        }

        .static-header-logo img {
            max-height: 70px;
            height: auto;
            display: inline-block;
        }

        .pdf-header-logo img {
            max-height: 70px;
            height: auto;
            display: inline-block;
        }

        .cover-page {
            padding: 0;
            color: #ffffff;
            background-color: #f7f2f6;
            position: relative;
            box-sizing: border-box;
            display: block;
            width: 210mm;
            height: 297mm;
            margin: 0;
            overflow: hidden;
        }

        .cover-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            display: block;
            z-index: 0;
        }

        .cover-logo-wrap {
            text-align: center;
            position: relative;
            z-index: 1;
            padding-top: 40px;
        }

        .cover-logo {
            width: 650px;
            max-width: 90%;
            height: auto;
            display: inline-block;
            margin: 0;
        }

        .cover-box {
            margin-top: 220px;
            background: #eadfe9;
            padding: 28px 18px;
            max-width: 70%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .cover-title {
            font-size: 40px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #68095a;
        }

        .cover-address {
            margin-top: 50px;
            font-size: 18px;
            font-weight: 400;
            color: #68095a;
        }
    </style>
</head>

<body>
    <div class="page-bg"></div>
    <?php
    $logoSrc = '';
    
    $logoPathSetting = get_setting('logo');
    
    if (!empty($logoPathSetting)) {
        $logoFullPath = public_path($logoPathSetting);
    
        if (file_exists($logoFullPath)) {
            $extension = strtolower(pathinfo($logoFullPath, PATHINFO_EXTENSION));
    
            $mimeType = 'image/jpeg';
    
            if ($extension === 'png') {
                $mimeType = 'image/png';
            } elseif ($extension === 'gif') {
                $mimeType = 'image/gif';
            } elseif ($extension === 'webp') {
                $mimeType = 'image/webp';
            } elseif ($extension === 'svg' || $extension === 'svgz') {
                $mimeType = 'image/svg+xml';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'jpe', 'jfif'])) {
                $mimeType = 'image/jpeg';
            }
    
            $imageData = base64_encode(file_get_contents($logoFullPath));
    
            $logoSrc = 'data:' . $mimeType . ';base64,' . $imageData;
        }
    }
    
    $coverBgSrc = '';
    
    $coverBgPath = public_path('images/cover-page-bg.png');
    
    if (file_exists($coverBgPath)) {
        $coverExtension = strtolower(pathinfo($coverBgPath, PATHINFO_EXTENSION));
    
        $coverMimeType = 'image/jpeg';
    
        if ($coverExtension === 'png') {
            $coverMimeType = 'image/png';
        } elseif ($coverExtension === 'gif') {
            $coverMimeType = 'image/gif';
        } elseif ($coverExtension === 'webp') {
            $coverMimeType = 'image/webp';
        } elseif ($coverExtension === 'svg' || $coverExtension === 'svgz') {
            $coverMimeType = 'image/svg+xml';
        } elseif (in_array($coverExtension, ['jpg', 'jpeg', 'jpe', 'jfif'])) {
            $coverMimeType = 'image/jpeg';
        }
    
        $coverImageData = base64_encode(file_get_contents($coverBgPath));
    
        $coverBgSrc = 'data:' . $coverMimeType . ';base64,' . $coverImageData;
    }
    
    $coverLogoSrc = '';
    
    $coverLogoPath = public_path('images/oreva_pdf_logo1.svg');
    
    if (file_exists($coverLogoPath)) {
        $coverLogoExtension = strtolower(pathinfo($coverLogoPath, PATHINFO_EXTENSION));
    
        $coverLogoMimeType = 'image/jpeg';
    
        if ($coverLogoExtension === 'png') {
            $coverLogoMimeType = 'image/png';
        } elseif ($coverLogoExtension === 'gif') {
            $coverLogoMimeType = 'image/gif';
        } elseif ($coverLogoExtension === 'webp') {
            $coverLogoMimeType = 'image/webp';
        } elseif ($coverLogoExtension === 'svg' || $coverLogoExtension === 'svgz') {
            $coverLogoMimeType = 'image/svg+xml';
        } elseif (in_array($coverLogoExtension, ['jpg', 'jpeg', 'jpe', 'jfif'])) {
            $coverLogoMimeType = 'image/jpeg';
        }
    
        $coverLogoImageData = base64_encode(file_get_contents($coverLogoPath));
    
        $coverLogoSrc = 'data:' . $coverLogoMimeType . ';base64,' . $coverLogoImageData;
    }
    
    ?>
    @php($isPdf = $isPdf ?? false)
    @if ($isPdf)
        <div class="cover-page">
            
            @if (!empty($coverLogoSrc))
                <div class="cover-logo-wrap">
                    <img class="cover-logo" src="{{ $coverLogoSrc }}" alt="Logo">
                </div>
            @endif
            <div class="cover-box">
                <div class="cover-title">
                    {{ trim((string) ($orderData['list']->product_name ?? 'PLUMBING SPECS')) }}
                </div>
                <div class="cover-address">
                    {{ trim((string) ($orderData['list']->name ?? '')) }}
                </div>
            </div>
        </div>
        <div style="page-break-after: always;"></div>
    @endif
    @if ($logoSrc)
        @if ($isPdf)
            <div class="pdf-header-logo">
                <img src="{{ $logoSrc }}" alt="Logo">
            </div>
        @else
            <div style="width: 100%; margin: 0 0 5px 0; text-align: right;">
                <img src="{{ $logoSrc }}" alt="Logo"
                    style="max-height: 70px; height: auto; display: inline-block;">
            </div>
        @endif
    @endif
    <div class="invoice-box">
        @if ($isPdf && $logoSrc)
            <div class="static-header-logo">
                <img src="{{ $logoSrc }}" alt="Logo">
            </div>
        @endif
        <div class="invoice-header-wrapper">
            <div class="invoice-header-box">
                <table class="invoice-header-table" cellpadding="4" cellspacing="0" border="0">
                    <tr>
                        <td class="invoice-header-left">
                            <strong style="font-size: 1.5rem;">Oreva</strong><br>
                            <p style="margin: 0;">{{ get_setting('address') }}</p>
                            <p style="margin: 4px 0 0 0;">{{ get_setting('phone_number') }}</p>
                        </td>
                        <td class="invoice-header-right">
                            <strong style="font-size: 1.125rem;">Project id
                                #{{ $orderData['list']->id }}</strong><br>
                            Date Issued: {{ \Carbon\Carbon::now()->format('d M Y') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <table width="100%" cellpadding="10" cellspacing="0" border="1"
            style="border-collapse: collapse; border: 1px solid #ddd; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th
                        style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: rgba(104, 9, 90, 0.1);">
                        Client Information:
                    </th>
                    <th
                        style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: rgba(104, 9, 90, 0.1);">
                        Project Information:
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Name: {{ $orderData['customer']->name }}</p>
                    </td>
                    <td style="width: 50%; font-size: 14px; ">
                        <p style="font-size: 14px; margin: 5px 0;">Builder Name:
                            {{ $orderData['list']->builder_name }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Email: {{ $orderData['customer']->email }}</p>
                    </td>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Builder Email:
                            {{ $orderData['list']->contact_email }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Phone Number:
                            {{ $orderData['customer']->phone }}
                        </p>
                    </td>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Phone Number:
                            {{ $orderData['list']->contact_number }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">ID: {{ $orderData['customer']->id }}</p>
                    </td>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Address: {{ $orderData['list']->name }},
                            {{ $orderData['list']->suburb }}, {{ $orderData['list']->state }},
                            {{ $orderData['list']->pincod }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="100%" cellpadding="10" cellspacing="0" border="1"
            style="border-collapse: collapse; border: 1px solid #ddd; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th
                        style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: rgba(104, 9, 90, 0.1); width: 30%;">
                        Item Information
                    </th>
                    <th
                        style="padding: 12px; text-align: center; border: 1px solid #ddd; background-color: rgba(104, 9, 90, 0.1); width: 35%;">
                        Product Image
                    </th>
                    <th
                        style="padding: 12px; text-align: center; border: 1px solid #ddd; background-color: rgba(104, 9, 90, 0.1); width: 35%;">
                        Product specification image
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderData['ordersData'] as $item)
                    <tr>
                        <?php
                        $productImage = is_array($item) ? $item['product_image'] ?? null : $item->product_image ?? null;
                        $specProductImage = is_array($item) ? $item['specification_product_image'] ?? null : $item->specification_product_image ?? null;
                        
                        $src = '';
                        $specSrc = '';
                        
                        if (!empty($productImage)) {
                            $imagePath = public_path('images/products/' . $productImage);
                        
                            if (file_exists($imagePath)) {
                                $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                        
                                $mimeType = 'image/jpeg';
                        
                                if ($extension === 'png') {
                                    $mimeType = 'image/png';
                                } elseif ($extension === 'gif') {
                                    $mimeType = 'image/gif';
                                } elseif ($extension === 'webp') {
                                    $mimeType = 'image/webp';
                                } elseif ($extension === 'svg' || $extension === 'svgz') {
                                    $mimeType = 'image/svg+xml';
                                } elseif (in_array($extension, ['jpg', 'jpeg', 'jpe', 'jfif'])) {
                                    $mimeType = 'image/jpeg';
                                }
                        
                                $imageData = base64_encode(file_get_contents($imagePath));
                        
                                $src = 'data:' . $mimeType . ';base64,' . $imageData;
                            }
                        }
                        
                        if (!empty($specProductImage)) {
                            $specImagePath = public_path('images/products/specification/' . $specProductImage);
                        
                            if (file_exists($specImagePath)) {
                                $specExtension = strtolower(pathinfo($specImagePath, PATHINFO_EXTENSION));
                        
                                $specMimeType = 'image/jpeg';
                        
                                if ($specExtension === 'png') {
                                    $specMimeType = 'image/png';
                                } elseif ($specExtension === 'gif') {
                                    $specMimeType = 'image/gif';
                                } elseif ($specExtension === 'webp') {
                                    $specMimeType = 'image/webp';
                                } elseif ($specExtension === 'svg' || $specExtension === 'svgz') {
                                    $specMimeType = 'image/svg+xml';
                                } elseif (in_array($specExtension, ['jpg', 'jpeg', 'jpe', 'jfif'])) {
                                    $specMimeType = 'image/jpeg';
                                }
                        
                                $specImageData = base64_encode(file_get_contents($specImagePath));
                        
                                $specSrc = 'data:' . $specMimeType . ';base64,' . $specImageData;
                            }
                        }
                        ?>
                        <td style="padding: 12px; text-align: left; border: 1px solid #ddd; vertical-align: top;">
                            <strong>Product name:</strong>
                            {{ is_array($item) ? $item['product_name'] ?? '' : $item->product_name ?? '' }}<br>
                            <br>
                            <strong>Product Code:</strong>
                            {{ is_array($item) ? $item['product_code'] ?? '' : $item->product_code ?? '' }}<br>
                            <br>
                            <strong>Supplier:</strong> Oreva<br>
                            <br>
                            <strong>Qty:</strong>
                            {{ is_array($item) ? $item['quantity'] ?? '' : $item->quantity ?? '' }}<br>
                            <br>
                            <strong>Note:</strong>
                            {{ is_array($item) ? $item['comment'] ?? '' : $item->comment ?? '' }}
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #ddd; vertical-align: top;">
                            @if ($src)
                                <img src="{{ $src }}" alt="Product Image" style="max-width: 220px; max-height: 160px; height: auto;">
                            @else
                                No image
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #ddd; vertical-align: top;">
                            @if ($specSrc)
                                <img src="{{ $specSrc }}" alt="Specification Image" style="max-width: 220px; max-height: 160px; height: auto;">
                            @else
                                No image
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!empty($orderData['posCustomerName'] ?? null) || !empty($orderData['posCustomerSignature'] ?? null))
            <table width="100%" cellpadding="10" cellspacing="0" border="0" style="margin-top: 10px;">
                <tr>
                    <td style="width: 50%; font-size: 14px; vertical-align: top;">
                        @if (!empty($orderData['posCustomerName'] ?? null))
                            <p style="margin: 0 0 6px 0;"><strong>Customer Name:</strong>
                                {{ $orderData['posCustomerName'] }}
                            </p>
                        @endif
                    </td>
                    <td style="width: 50%; font-size: 14px; vertical-align: top; text-align: right;">
                        @if (!empty($orderData['posCustomerSignature'] ?? null))
                            <div style="display: inline-block; text-align: center;">
                                <div style="border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-bottom: 4px;">
                                    <img src="{{ $orderData['posCustomerSignature'] }}" alt="Customer Signature"
                                        style="max-width: 180px; max-height: 80px;">
                                </div>
                                <span style="font-size: 12px; color: #777;">Customer Signature</span>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        @endif
        <div class="note" style="font-size: 14px; color: #555; margin-top: 20px;">
            Thank you for your business!
        </div>
    </div>
</body>

</html>
