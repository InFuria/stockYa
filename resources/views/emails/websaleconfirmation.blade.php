<!DOCTYPE html>
<html>

<style>
    #tblContainer{
        border: solid #fbab04 2px;
        border-top: none;
        width: 700px;
        height: auto;
        text-align: center;
        margin: auto;
    }

    .banner {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>


<body>
<div class="container">

    <div style="{{ $banner_style }}; text-align: center;">
        <img class="banner" src="https://kaizen-donarosa.com/api/files/190" width="704px" height="110px" style="border:none; text-decoration:none;">
    </div>

    <div id="tblContainer">
        <div style="text-align: center">
            <h3 style="font:normal bold 1.625em/1.73em TrebuchetMS; letter-spacing:0.4px; color:#333333; padding-top: 40px">DETALLES DEL PEDIDO</h3>
            <p style="width: 52px; border-top:3px solid darkorange; margin: auto"></p>
            <h4 style="letter-spacing:0.4px!important; color:#595959; font:italic bold 1.438em/1.52em TrebuchetMS; padding-top: 20px">
                A continuación podrá encontrar el detalle de su pedido e información importante:
            </h4>
        </div>

        <div class="row" style="text-align: center;">
            <div class="col-12">
                <table align="center" width="82%" style="width:594px; font:normal normal 0.938em/2.33em TrebuchetMS; color:#333333; letter-spacing:0.2px;">
                    <tbody align="left">
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Pedido No.:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['id'] }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Codigo de seguimiento:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['tracker']  }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Cliente:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['client_name'] }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Email:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['email'] }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Teléfono:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ isset($order['phone']) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Fecha:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['created_at'] }}</td>
                    </tr>
                    <tr>
                        <td width="39%" style="padding-right:1%"><strong>Detalles de Entrega:</strong></td>
                        <td width="60%" style="color:#7b7b7b">{{ $order['text'] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-12" style="text-align: center; margin-top: 30px">
                <h3 style="font:normal bold 1.625em/1.73em TrebuchetMS; letter-spacing:0.4px; color:#333333">PRODUCTOS</h3>
                <p style="width: 52px; border-top:3px solid darkorange; margin: auto"></p>
            </div>

            <div class="col-12">
                <table align="center" cellpadding="5" cellspacing="5" width="82%" style="width:594px; font:italic normal 0.938em/2em TrebuchetMS; color:#333333; letter-spacing:0.2px; padding-bottom:27px">
                    <tbody>
                    <tr style="padding-bottom:42px">
                        <td width="auto" align="left">Producto</td>
                        <td align="center">PrecioU.</td>
                        <td align="center">Cantidad</td>
                        <td align="center">Total</td>
                    </tr>

                    @foreach($products as $product)
                        <tr style="color:#807f7f">
                            <td width="46%" align="left"><li>{{ $product['detail']['name'] }}</li></td>
                            <td width="14%" align="center">{{ $product['detail']['price'] . ' $'}}</td>
                            <td width="12%" align="center">{{ $product['quantity'] }}</td>
                            <td width="14%" align="center">{{ $product['subtotal'] . ' $'}}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="2" align="right" style="color:#807f7f; font-family:TrebuchetMS">
                            <p style="margin:0px; padding-right:5%"><strong></strong></p>
                        </td>
                    </tr>

                    <tr align="right">
                        <td colspan="3" style="color:#4a4a4a; font-family:TrebuchetMS">
                            <strong>Sub Total:</strong>
                        </td>

                        <td colspan="2" style="color:#807f7f; font-family:TrebuchetMS">
                            <p style="margin:0px; padding-right:5%"><strong>{{ $order['total'] . ' $'}}</strong></p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" align="right" style="color:#4a4a4a; font-family:TrebuchetMS">
                            <strong>Costo de Entrega:</strong>
                        </td>

                        <td colspan="2" align="right" style="color:#807f7f; font-family:TrebuchetMS">
                            <p style="margin:0px; padding-right:5%"><strong>{{ $delivery . ' $'}}</strong></p></td>
                    </tr>

                    <tr>
                        <td colspan="3" align="right" style="color:#4a4a4a; font-family:TrebuchetMS">
                            <strong>TOTAL:</strong></td>

                        <td colspan="2" align="right" style="color:#807f7f; font-family:TrebuchetMS">
                            <p style="margin:0px; padding-right:5%"><strong>{{ $order['total'] + $delivery . ' $'}}</strong></p></td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 20px"></td>
                    </tr>

                    <tr>
                        <td align="center" colspan="5" style="padding-top:21px; color:#4a4a4a; border-top: solid black 1px">
                            <strong>"No válido como documento legal"</strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
