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
                }

                /* ----- Header ----- */
                .header td{
                    padding: 30px 15px 30px 15px !important;
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
                padding: 5px 30px 5px 0 !important;
            }

            .table__content{
                padding: 5px 5px 5px 0 !important;
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
        <table class="full-width-container" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#eeeeee" style="width: 100%; height: 100%; padding: 30px 0 30px 0;">
            <tr>
                <td align="center" valign="top">
                    <!-- / 700px container -->
                    <table class="container" border="0" cellpadding="0" cellspacing="0" width="700" bgcolor="#ffffff" style="width: 700px;">
                        <tr>
                            <td align="center" valign="top">

                                <!-- / Hero subheader -->
                                <table class="container hero-subheader" border="0" cellpadding="0" cellspacing="0" width="620" style="width: 620px;">
                                    <tr>
                                        <td class="hero-subheader__title" style="font-size: 43px; font-weight: bold; padding: 40px 0 15px 0;" align="left">{{ $title }}</td>
                                    </tr>

                                    <tr>
                                        <td class="hero-subheader__content" style="font-size: 16px; line-height: 27px; color: #969696; padding: 0 60px 0 0;" align="left">
                                            {!! $content !!}
                                        </td>
                                    </tr>
                                </table>
                                <!-- /// Hero subheader -->

                                <!-- / Paragraph -->
                                <table class="container paragraph-block" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="center" valign="top">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" style="width: 620px;">
                                                <tr>
                                                    <td class="paragraph-block__content" style="padding: 30px 0 30px 0; font-size: 16px; line-height: 27px; color: #969696;" align="left">
                                                        @if(is_array($table) && !empty($table))
                                                            <table class="table">
                                                                @foreach ($table as $row)
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
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /// Paragraph -->

                                <!-- / Divider -->
                                <table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 0;" align="center">
                                    <tr>
                                        <td align="center">
                                            <table class="container" border="0" cellpadding="0" cellspacing="0" width="620" align="center" style="border-bottom: solid 1px #eeeeee; width: 620px;">
                                                <tr>
                                                    <td align="center">&nbsp;</td>
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
                                                    <td style="color: #d5d5d5; text-align: center; font-size: 15px; padding: 30px 0 30px 0; line-height: 22px;">
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
