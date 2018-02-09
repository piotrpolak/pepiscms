<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>API Documentation</title>
        <style type="text/css">
            <!--
            * {
                font-family: Arial, Helvetica, sans-serif;
                margin: 0px;
                padding: 0px;
            }
            body {
                margin: 20px;
            }
            h1 {
                margin-bottom: 30px;
                font-size: 22px;
            }
            h2 {
                font-size: 14px;
                margin-top: 20px;
            }
            p {
                color: #666666;
                margin-top: 10px;
                font-size: small;
            }
            p.signature {
                color: #333;
                margin-top: 5px;
                font-style: italic;
            }
            -->
        </style>
    </head>

    <body>
        <h1>Web service API documentation</h1>
        <p>Notice: access to these methods might require authorization with API KEY. Look in the manual for reference.</p>
        <?php foreach ($functions as $function): ?>
            <h2><?= end(explode('.', $function['function'])) ?></h2>
            <p class="signature"><?= $function['signature_label'] ?></p>
            <p><?= $function['docstring'] ?></p>
        <?php endforeach; ?>
        </table>
    </body>
</html>
