<div id="div_print">
    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr><td>&nbsp;</td></tr><?php 
        if (!empty($post)) { ?>
            <tr>
                <td id="print_btn" align="left" width="70%"><?php 
                    if ((isset($post['show_print']) && $post['show_print'] == "Y") || (isset($post['show_export']) && $post['show_export'] == "Y") || (isset($post['show_back']) && $post['show_back'] == "Y")) { ?>
                        <button class="btn btn-default" value="Close" onclick="closeWindow()">Close</button>
						<?php
                    }
                    if (isset($post['show_print']) && $post['show_print'] == "Y") {?>
                        <button  onclick="printReport()" type="Button" name="" class="btn btn-primary" value="Print">Print</button><?php
                    }
                    if (isset($post['show_export']) && $post['show_export'] == "Y") {?>
                        <button class="btn btn-success" id="report_xls_export_btn" onclick="exportDataToExcel();" type="Button" name="" value="Export Excel">Export Excel</button><?php
                    }
                    if (isset($post['show_pdf']) && $post['show_pdf'] == "Y") {?>
                        <input class="btn red" id="report_pdf_export_btn" onclick="exportDataToPDF();" type="Button" name="" value="Export PDF"><?php
                    }?>
                </td><?php
                if (isset($post['show_back']) && $post['show_back'] == "Y") {?>
                    <td id="back_search" height="30" align="right">
                        <button class="btn btn-default" onclick="backToSearchArea();" value="<< Back To Search"><< Back To Search</button>
                    </td><?php 
                }?>
            </tr><?php 
        } ?>
    </table>
</div>
<a class="btn green d-none" id="msg-modal" data-toggle="modal" data-backdrop="static" data-keyboard="false" href="#stack2">Launch modal</a>
<div id="stack2" class="modal fade" tabindex="-1" data-width="650">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="msg-html"></div>
        </div>
    </div>
</div>