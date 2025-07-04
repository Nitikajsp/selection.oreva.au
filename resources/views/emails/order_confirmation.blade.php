    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <style>
            
            body {

                font-family: Arial, sans-serif;
                background-color: #f8f8f8;
                margin: 0;
                padding: 20px;
            }

            .invoice-box {

                width: 90%;
                max-width: 800px;
                margin: auto;
                padding: 20px;
                border: 1px solid #eee;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                border-radius: 6px;
            }

        </style>
    </head>
    
    <body>
        <div class="invoice-box">
            <table width="100%" cellpadding="10" cellspacing="0" border="0" style="margin-bottom: 20px; background-color: rgba(47,43,61,.06); border-radius: 6px;">
                <tr>
                    <td style="font-size: 14px; line-height: 2.4; padding-right: 100px; vertical-align: text-top">
                        <strong style="font-size: 1.5rem;">Oreva</strong><br>
                        <p>{{ get_setting('address') }}</p>
                        <p>{{ get_setting('phone_number') }}</p>

                        </td>
                    <td style="font-size: 14px; line-height: 2.4;">
                        <strong style="font-size: 1.125rem;">Project id #{{ $orderData['list']->id }}</strong><br>
                        Date Issued: {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </td>
                </tr>                                  

            </table>

            <table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; border: 1px solid #ddd; margin-bottom: 20px;">
            <thead>
            <tr>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f5f5f5;">Client Information:</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f5f5f5;">Project Information:</th>
                    </tr>
        </thead>
        <tbody>

                <tr>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Name: {{ $orderData['customer']->name }}</p>
                     
                    </td>
                    <td style="width: 50%; font-size: 14px; ">
                        <p style="font-size: 14px; margin: 5px 0;">Builder Name: {{ $orderData['list']->builder_name }}</p>
                      
                    </td>
                </tr>
                
                <tr>
                    <td style="width: 50%; font-size: 14px;">
                      
                        <p style="font-size: 14px; margin: 5px 0;">Email: {{ $orderData['customer']->email }}</p>
                     
                    </td>
                    <td style="width: 50%; font-size: 14px;">
                        <p style="font-size: 14px; margin: 5px 0;">Builder Email: {{ $orderData['list']->contact_email }}</p>
                    </td>
                </tr>


                <tr>

                    <td style="width: 50%; font-size: 14px;">
                    <p style="font-size: 14px; margin: 5px 0;">Phone Number: {{ $orderData['customer']->phone }}</p>                     
                    </td>
                    <td style="width: 50%; font-size: 14px;">
                    <p style="font-size: 14px; margin: 5px 0;">Phone Number: {{ $orderData['list']->contact_number }}</p>                       
                    </td>

                </tr>

                <tr>
                    <td style="width: 50%; font-size: 14px;">
                      
                    <p style="font-size: 14px; margin: 5px 0;">ID: {{ $orderData['customer']->id }}</p>            
                        </td>
                    <td style="width: 50%; font-size: 14px;">
                    <p style="font-size: 14px; margin: 5px 0;">Address: {{ $orderData['list']->name }}, {{ $orderData['list']->suburb }}, {{ $orderData['list']->state }}, {{ $orderData['list']->pincod }}</p> </td>
                </tr>
                

        </tbody>

            </table>    

            <table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; border: 1px solid #ddd; margin-bottom: 20px;">

                <thead>
                    <tr>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f5f5f5;">Image</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f5f5f5;">Item</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f5f5f5;">Qty</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($orderData['ordersData'] as $item)
                    <tr>
                    <?php
                        //$imagePath = 'http://selection.oreva.au/images/products/' . $item['product_image'];
                        $imagePath = url('images/products/' . $item['product_image']);
                        $imageData = base64_encode(file_get_contents($imagePath));
                        $src = 'data:image/jpeg;base64,' . $imageData;
                        ?>
                        <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><img src="{{ $src }}" alt="Product Image" style="max-width: 50px; height: auto;"></td>
                        <td style="padding: 12px; text-align: left; border: 1px solid #ddd;">{{ $item['product_name'] }}<br>
                        <strong class="text-secondary">Commnt :</strong> <span class="text-secondary">{{ $item['comment'] }}</span>

                        </td>
                        <td style="padding: 12px; text-align: left; border: 1px solid #ddd;">{{ $item['quantity'] }}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
   
            <div class="note" style="font-size: 14px; color: #555; margin-top: 20px;">
                Thank you for your business!
            </div>
        </div>
    </body>
    </html>
