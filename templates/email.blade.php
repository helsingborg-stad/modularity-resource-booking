<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>{{ $title }}</title>
        <style type="text/css">
            /* ----- Custom Font Import ----- */
            @import url(https://fonts.googleapis.com/css?family=Roboto:400,700,400italic,700italic&subset=latin,latin-ext);

            /* ----- Text Styles ----- */
            table {
                font-family: 'Roboto', Arial, sans-serif;
                -webkit-font-smoothing: antialiased;
                -moz-font-smoothing: antialiased;
                font-smoothing: antialiased;
            }

            @media only screen and (max-width: 700px){
                /* ----- Base styles ----- */
                .full-width-container{
                    padding: 0 !important;
                }

                .container{
                    width: 100% !important;
                    border-radius: 3px !important;
                    box-shadow: 0 0 10px 10px rgba(0,0,0,0.01) !important;
                }

                /* ----- Header ----- */
                .header td{
                    padding: 32px 15px 32px 15px !important;
                }

                /* ----- Title block ----- */
                .title-block{
                    padding: 0 15px 0 15px;
                }

                /* ----- Paragraph block ----- */
                .paragraph-block__content{
                    padding: 25px 15px 18px 15px !important;
                }
            }

            /* ----- Table block ----- */
            .table {
                padding: 0 !important;
            }

            .table tr {
                padding: 0 !important;
            }

            .table__heading {
                color: #000;
                padding: 5px 32px 5px 0 !important;
            }

            .table__content{
                padding: 5px 5px 5px 0 !important;
            }

            .text-left {
                text-align: left;
            }

            .text-right {
                text-align: right;
            }

            .summary-item td {
                border-bottom: solid 1px #eeeeee;
                padding-bottom: 32px;
                padding-top: 32px;
            }
        </style>

        <!--[if gte mso 9]><xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml><![endif]-->
    </head>

    <body style="padding: 0; margin: 0;" bgcolor="#eeeeee">
        <span style="color:transparent !important; overflow:hidden !important; display:none !important; line-height:0px !important; height:0 !important; opacity:0 !important; visibility:hidden !important; width:0 !important; mso-hide:all;">{{ $preheader }}</span>

        <!-- / Full width container -->
        <table class="full-width-container" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#eeeeee" style="width: 100%; height: 100%; padding: 32px 0 32px 0;">
            <tr>
                <td align="center" valign="top">
                    <!-- / 700px container -->
                    <table class="container" border="0" cellpadding="0" cellspacing="0" width="700" bgcolor="#ffffff" style="width: 700px; box-shadow: 0 0 10px 10px rgba(0,0,0,0.01); border-radius: 3px;">
                        <tr>
                            <td align="center" valign="top">

                                <!-- / Hero header -->
                                <table border="0" cellpadding="0" cellspacing="0" width="700" bgcolor="{{ $color }}" style="width: 700px; background: {{ $color }}; padding: 40px; border-radius: 3px 3px 0 0; margin-bottom: 32px;">
                                    <tr>
                                        <td>
                                            <!-- / Hero subheader -->
                                            <table class="container hero-subheader" border="0" cellpadding="0" cellspacing="0" width="620" style="width: 620px;">
                                                <tr>
                                                    <td class="hero-subheader__title" style="font-size: 43px; font-weight: bold; padding: 40px 0 5px 0; color: #ffffff; text-transform: uppercase;" align="left">{{ $subject }}</td>
                                                </tr>

                                                <tr>
                                                    <td class="hero-subheader__content" style="font-size: 18px; line-height: 32px; color: #ffffff; padding: 0 60px 0 0;" align="left">
                                                        {!! $content !!}
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- /// Hero subheader -->
                                        </td>
                                    </tr>
                                </table>


                                <!-- / Table Content -->
                                @if (is_array($sections) && !empty($sections))
                                @foreach ($sections as $section)
                                    @if (is_array($section['table']) && !empty($section['table']))
                                    <table class="container paragraph-block" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" valign="top">
                                                <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" style="width: 620px;">
                                                    <tr>
                                                        <td class="paragraph-block__content" style="padding: 32px 0 0 0; font-size: 16px; line-height: 27px; color: #969696;" align="left">
                                                                <table class="table">
                                                                        <tr>
                                                                        <td><h2 style="color: {{$color}}; margin-top: 0;">{{$section['title']}}</h2></td>
                                                                        </tr>
                                                                    @foreach ($section['table'] as $row)
                                                                        <tr>
                                                                            <td class="table__heading">
                                                                                <strong>{{ $row['heading'] }}</strong>
                                                                            </td>
                                                                            <td class="table_content">
                                                                                {{ $row['content'] }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    @endif
                                @endforeach
                                @endif
                                <!-- /// Table Content -->

                                <!-- / Summary -->
                                @if (isset($summary) && isset($summary['items']) && is_array($summary['items']) && !empty($summary['items']))
                                <table class="container paragraph-block" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="center" valign="top">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" style="width: 620px;">
                                                <tr>
                                                    <td class="paragraph-block__content" style="padding: 32px 0 0 0; font-size: 16px; line-height: 27px;" align="left">
                                                            <table class="table" style="width: 100%;">
                                                                <thead>
                                                                <tr>
                                                                    <th align="left">
                                                                        <h2 style="color: {{$color}}; margin: 0;">{{$summary['title']}}</h2>
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                @foreach ($summary['items'] as $item)
                                                                    <tr class="summary-item">
                                                                        <td>
                                                                            <strong>{{ $item['title'] }}</strong>
                                                                            @if (isset($item['week']) && !empty($item['week']))
                                                                                <br><small><strong>{{$item['week']}}</strong></small>
                                                                            @endif
                                                                            @if (isset($item['content']) && !empty($item['content']))
                                                                            <br>
                                                                                @if (is_string($item['content']))
                                                                                    <small>{{$item['content']}}</small>
                                                                                @elseif (is_array($item['content']))
                                                                                    @foreach ($item['content'] as $content)
                                                                                        <small>{{$content}}</small><br>
                                                                                    @endforeach
                                                                                @endif
                                                                            @endif
                                                                        </td>
                                                                        <td  class="text-right">
                                                                            <strong>{{ $item['price'] }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tfoot>
                                                                    <tr>
                                                                        <td>
                                                                            <h2 style="color: {{$color}};  margin: 0;">{{$summary['totalTitle']}}</h2>
                                                                        </td>
                                                                        <td  class="text-right">
                                                                        <h2 style="color: {{$color}}; margin-bottom: 0px; margin-top: 32px;">{{ $summary['totalPrice'] }}</h2>
                                                                        <h4 style="color: {{$color}}; margin-top: 0px; margin-bottom: 16px;">{{$vat}}</h4>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                                <!-- /// Summary -->

                                <!-- / Links -->
                                @if (isset($links) && is_array($links) && !empty($links))
                                <table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                                    <tr>
                                        <td align="center">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" align="center" style="width: 620px;">
                                                <tr>
                                                    <td style="padding: 32px 0;"  align="center">
                                                        @foreach ($links as $link)
                                                        <a href="{{ $link['url'] }}" target="_blank" style="color: #fff; font-size:18px; text-decoration: none; border-radius: 3px; margin-right: 5px; background-color: {{ $color }}; border: 12px solid {{ $color }}; display: inline-block; padding: 0 10px;">
                                                            <span style="color:#fff">{{ $link['text'] }}</span>
                                                        </a>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                                <!-- /// Links -->

                                <!-- / Divider -->
                                <table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 0;" align="center">
                                    <tr>
                                        <td align="center">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" align="center" style="border-bottom: solid 1px #eeeeee; width: 620px;">
                                                <tr>

                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /// Divider -->

                                <!-- / Footer -->
                                <table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                                    <tr>
                                        <td align="center">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" align="center" style="width: 620px;">
                                                <tr>
                                                    <td style="color: #d5d5d5; text-align: center; font-size: 15px; padding: 32px 0 32px 0; line-height: 22px;">
                                                        This is a automatically generated email. You cannot respond to this email.
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /// Footer -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
