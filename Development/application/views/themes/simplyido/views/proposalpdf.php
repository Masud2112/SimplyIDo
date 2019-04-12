<?php
$CI =& get_instance();
/*echo "<pre>";
print_r($proposal->pmt_sdl_template);
die('<--here');*/
$rel_content = $proposal->rel_content;
$clients = $proposal->clients;
$venue = $proposal->venue;
$quotes = $proposal->quotes;
if (isset($proposal) && $proposal->ps_template > 0) {
    $paymentschedule = $proposal->pmt_sdl_template['paymentschedule'];
    $duedate_types = $proposal->pmt_sdl_template['duedate_types'];
    $duedate_criteria = $proposal->pmt_sdl_template['duedate_criteria'];
    $duedate_duration = $proposal->pmt_sdl_template['duedate_duration'];
    $amount_types = $proposal->pmt_sdl_template['amount_types'];
}

$proposalversion = format_proposal_number($proposal);
if(isset($proposal->feedback)){
    $selectedItems = json_decode($proposal->feedback->selected_items);
}


if (isset($rel_content) && !empty($rel_content)) {
    if (isset($proposal)) {
        if ($proposal->issued_date != '0000-00-00') {
            /*$proposal->issued_date = date('l, F d, Y', strtotime($proposal->issued_date));*/
            $proposal->issued_date = date('m/d/Y', strtotime($proposal->issued_date));
        } else {
            /*$proposal->issued_date = date('l, F d, Y', strtotime($proposal->datecreated));*/
            $proposal->issued_date = date('m/d/Y', strtotime($proposal->datecreated));
        }
        if ($proposal->valid_date != '0000-00-00') {
            /*$proposal->valid_date = date('l, F d, Y', strtotime($proposal->valid_date));*/
            $proposal->valid_date = date('m/d/Y', strtotime($proposal->valid_date));
        } else {
            /*$proposal->valid_date = date('l, F d, Y', strtotime('+10 days', strtotime($proposal->datecreated)));*/
            $proposal->valid_date = date('m/d/Y', strtotime('+10 days', strtotime($proposal->datecreated)));
        }
    }
    $ename = $rel_content->name;
    $edate = date('l, F d, Y', strtotime($rel_content->eventstartdatetime));
    $edate_end = _dt($rel_content->eventenddatetime);

    $esdate = date_create($rel_content->eventstartdatetime);
    $esdate = date_format($esdate, "h:i A");

    $eenddate = date_create($rel_content->eventenddatetime);
    $eenddate = date_format($eenddate, "h:i A");
    if (strtotime($edate) > strtotime($edate_end)) {
        $edate_end = _dt($rel_content->eventstartdatetime);
    } else {
        $edate_end = "";
    }

    if (isset($venue)) {
        $v_name = $venue->venuename;
        $v_location = $venue->venueaddress . " " . $venue->venueaddress2;
        $v_state = $venue->venuestate;
        $v_city = $venue->venuecity;
        $v_zip = $venue->venuezip;
    }
}

$dimensions = $pdf->getPageDimensions();
//$eventstartdatetime = date('l, F j, Y', strtotime($proposal->rel_content->eventstartdatetime));
//$eventstarttime = date('h:i A', strtotime($proposal->rel_content->eventstartdatetime));
$proposal_number = format_proposal_number($proposal);
// Tag - used in BULK pdf exporter
if ($tag != '') {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(245, 245, 245);
    $pdf->SetXY(0, 0);
    $pdf->SetFont($font_name, 'B', 15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35, 109, 235);
    $pdf->Cell(100, 1, mb_strtoupper($tag, 'UTF-8'), 'TB', 0, 'C', '1');
    $pdf->StopTransform();
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setX(10);
    $pdf->setY(10);
    $pdf->setEqualColumns(3, 57);
}

$info_right_column = '';
$info_left_column = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('Proposal') . '</span><br />';

// write the first column
$info_left_column .= pdf_logo_url();

$proposalstatus = "";

$src = "";
if (!empty($proposal->banner)) {
    $path = get_upload_path_by_type('proposal_banner_images') . $proposal->templateid . '/' . $proposal->banner;
    if (file_exists($path)) {
        $path = get_upload_path_by_type('proposal_banner_images') . $proposal->templateid . '/croppie_' . $proposal->banner;
        $src = base_url() . 'uploads/proposals_images/banner/' . $proposal->templateid . '/' . $proposal->banner;
        if (file_exists($path)) {
            $src = base_url() . 'uploads/proposals_images/banner/' . $proposal->templateid . '/croppie_' . $proposal->banner;
        }
    }
}else{
    $src=base_url('assets/images/default_banner.jpg');
}

$proposal_banner = '<div class="bannerImg"><img src="' . $src . '" style="max-width: 100%"/></div>';

$proposalheader1Row = '<table width="100%" bgcolor="#fff">';
$proposalheader1Row .= '<tr width="100%">';
$proposalheader1Row .= '<td width="33.33%" align="left">' . $info_left_column . '</td>';
$proposalheader1Row .= '<td width="33.33%" align="center"><h1>' . ucfirst(_l('cover_page')) . '</h1></td>';
$proposalheader1Row .= '<td width="33.33%" align="right" style="text-align: right">' . $info_right_column . '</td></tr>';
$proposalheader1Row .= '</table>';

$proposalheader2Row = '<table width="100%" bgcolor="#fff">';
$proposalheader2Row .= '<tr width="100%">';
$proposalheader2Row .= '<td width="33.33%" align="left">' . $info_left_column . '</td>';
$proposalheader2Row .= '<td width="33.33%" align="center"><h1>' . ucfirst(_l('quote')) . '</h1></td>';
$proposalheader2Row .= '<td width="33.33%" align="right" style="text-align: right">' . $info_right_column . '</td></tr>';
$proposalheader2Row .= '</table>';

$proposalheader3Row = '<table width="100%" bgcolor="#fff">';
$proposalheader3Row .= '<tr width="100%">';
$proposalheader3Row .= '<td width="33.33%" align="left">' . $info_left_column . '</td>';
$proposalheader3Row .= '<td width="33.33%" align="center"><h1>' . ucfirst(_l('agreement')) . '</h1></td>';
$proposalheader3Row .= '<td width="33.33%" align="right" style="text-align: right">' . $info_right_column . '</td></tr>';
$proposalheader3Row .= '</table>';

$proposalheader4Row = '<table width="100%" bgcolor="#fff">';
$proposalheader4Row .= '<tr width="100%">';
$proposalheader4Row .= '<td width="33.33%" align="left">' . $info_left_column . '</td>';
$proposalheader4Row .= '<td width="33.33%" align="center"><h1>' . ucfirst(_l('invoice')) . '</h1></td>';
$proposalheader4Row .= '<td width="33.33%" align="right" style="text-align: right">' . $info_right_column . '</td></tr>';
$proposalheader4Row .= '</table>';

$proposalheader5Row = '<table width="100%" bgcolor="#fff">';
$proposalheader5Row .= '<tr width="100%">';
$proposalheader5Row .= '<td width="33.33%" align="left">' . $info_left_column . '</td>';
$proposalheader5Row .= '<td width="33.33%" align="center"><h1>'.ucfirst(_l('payment')).'</h1></td>';
$proposalheader5Row .= '<td width="33.33%" align="right" style="text-align: right">' . $info_right_column . '</td></tr>';
$proposalheader5Row .= '</table>';



$proposalbannerRow = '<table width="100%"><tr><td>' . $proposal_banner . '</td></tr></table>';

/*$proposalRow = '<table width="100%">';
$proposalRow .= '<tr><td>';*/
$proposalRow = '<table width="100%"><tr>';
$proposalRow .= '<td align="center">';
if (isset($rel_content) && !empty($rel_content)) {
    ob_start();
    ?>

    <?php if (isset($rel_content->profile_image)) {
        $profileImagePath = 'uploads/lead_profile_images/' . $rel_content->id . '/thumb_' . $rel_content->profile_image;
        if (file_exists($profileImagePath)) {
            echo lead_profile_image($rel_content->id, array(),'round',array('width'=>100));
        } else {
            echo substr($ename, 0, 1);
        }
    } else {

        if (isset($rel_content->project_profile_image)) {
            $profileImagePath = 'uploads/project_profile_images/' . $rel_content->id . '/thumb_' . $rel_content->project_profile_image;
        }

        if (isset($profileImagePath) && file_exists($profileImagePath)) {
            echo project_profile_image($rel_content->id, array(),'round',array('width'=>100));
        } else {
            echo substr($ename, 0, 1);
        }
    } ?>
    <br/><br/><b><?php echo $ename; ?></b><br/>
    <?php echo $edate; ?><?php echo !empty($edate_end) ? " - " . $edate_end : "" ?><br/>
    <?php echo $esdate; ?> - <?php echo $eenddate; ?><br/>
    <?php if (isset($venue)) { ?>

        <br/><b><?php echo $v_name; ?></b><br/>
        <?php echo $v_location; ?><br/>
        <?php echo $v_city . ", " . $v_state . ", " . $v_zip; ?><br/>
    <?php } ?>

    <?php $proposalRowsection1 = ob_get_contents();
    ob_get_clean();
    ob_start(); ?>
    <?php
    $clients = array_values($clients);
    echo addressbook_pdf_profile_image($clients[0]['id'], array(),'round',array('width'=>100)); ?>
    <br/><br/>
    <?php if (isset($clients) && !empty($clients)) {
        foreach ($clients as $client) {
            $name = $client['firstname'] . " " . $client['lastname'];
            $email = isset($client['email']) ? $client['email'] : "";
            $phone = isset($client['phone']) ? $client['phone'] : "";
            ?>
            <b><?php echo $name; ?></b><br/>
            <?php echo $email; ?><br/>
            <?php echo $phone; ?><br/>
            <br/>
        <?php }
    } ?>
    <?php
    $proposalRowsection2 = ob_get_contents();
    ob_get_clean();
    ob_start(); ?>
    <?php /*$company_logo = get_brand_option('company_logo');
    if ($company_logo != '') {
        $clogoImagePath = FCPATH . 'uploads/brands/round_' . $company_logo;
        $src = base_url('uploads/brands/' . $company_logo);
        if(file_exists($clogoImagePath)){
            $src = base_url('uploads/brands/round_' . $company_logo);
        }
        echo '<img width="100" src="' . $src. '" class="img-responsive" alt="' . get_brand_option('companyname') . '">';
    } else if (isset($company_name) && $company_name != '') {
        echo $company_name ;
    } else {
        echo '';
    }
    */
    echo pdf_logo_url();
    ?>
    <br /><br />
        <strong>Proposal #:</strong> P-<?php echo $proposalversion ?><br />
        <strong>Issued :</strong>
            <?php echo isset($proposal) ? $proposal->issued_date : date('m/d/Y') ?><br />
            <strong>Valid Until:</strong>
            <?php echo isset($proposal) ? $proposal->valid_date : date('m/d/Y', strtotime('+10 days')) ?><br /><br />
        <?php echo get_brand_option('invoice_company_name'); ?><br />
        <?php echo get_brand_option('smtp_email'); ?><br />
        <?php echo get_brand_option('invoice_company_phone');
            if (get_brand_option('invoice_company_phone_ext') != "") {
                echo " X " . get_brand_option('invoice_company_phone_ext');
            }
            ?>
    <br />

    <?php
    $proposalRowsection3 = ob_get_contents();
    ob_get_clean();
}
$proposalRow .= $proposalRowsection1 . '</td>';
$proposalRow .= '<td align="center">' . $proposalRowsection2 . '</td>';
$proposalRow .= '<td align="center">' . $proposalRowsection3 . '</td>';
$proposalRow .= '</tr></table><p></p><hr><p></p><br pagebreak="true"/>';
//$proposalRow .= '</td></tr></table>';

$pdf->writeHTML($proposalheader1Row, true, false, false, false, '');
$pdf->writeHTML($proposalbannerRow, true, false, false, false, '');
$pdf->writeHTML($proposalRow, true, false, false, false, '');

ob_start();
include "proposalpdf/proposal_quotes.php";
$proposalRow = ob_get_contents();
ob_get_clean();

$proposalRow = str_replace('&nbsp', '', $proposalRow);
/*echo $proposalRow;
die('<--here');*/
$pdf->writeHTML($proposalheader2Row, true, false, false, false, '');
$pdf->writeHTML($proposalRow, true, false, false, false, '');

ob_start();
include "proposalpdf/agreement.php";
$proposalRow = ob_get_contents();
ob_get_clean();


$pdf->writeHTML($proposalheader3Row, true, false, false, false, '');
$pdf->writeHTML($proposalRow, true, false, false, false, '');

if(isset($proposal->invoices) && !empty($proposal->invoices)){
    ob_start();
    include "proposalpdf/invoice.php";
    $proposalRow = ob_get_contents();
    ob_get_clean();

    $pdf->writeHTML($proposalheader4Row, true, false, false, false, '');
    $pdf->writeHTML($proposalRow, true, false, false, false, '');
}

/*$pdf->writeHTML($proposalheader5Row, true, false, false, false, '');
$pdf->writeHTML($proposalRow, true, false, false, false, '');*/

//$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
//$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
//$pdf->ln(6);
// Get Y position for the separation


/*$y = $pdf->getY();

$proposaleventInfo = '<div>';
$proposaleventInfo .= '</div>';

$proposaleventInfo2 = '<div>';
$proposaleventInfo2 .= '<b style="color:#4e4e4e;"># ' . $proposal_number . '</b>' . '<br />';
$proposaleventInfo2 .= '</div>';

$proposal_info = '';
$proposal_info .= '<div style="color:#424242;">';

$proposal_info .= format_organization_info();

$proposal_info .= '</div>'*/;

//$pdf->writeHTMLCell(0, '', '', $y * 1.5, $proposal_banner, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);

//$y = $pdf->getY();

//$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $proposaleventInfo, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
//$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $proposaleventInfo2, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
/*$pdf->ln(6);
$y = $pdf->getY();*/

//$pdf->writeHTMLCell(0, '', '', $y, '<hr style="background-color: #ccc"/>', 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);
//$y = $pdf->getY();

// Bill to
//$client_details = '<b>' . _l('invoice_bill_to') . '</b><br />';
/*$client_details = '';
$client_details .= '<div style="color:#424242;">';
$client_details .= '</div>';

$proposalstatus = "";

$proposalRow = '
<table width="100%" bgcolor="#fff">
    <tr>
        <td width="33.33%" align="left"><table width="100%" bgcolor="#fff">
        <tr><td width="25%" align="left">FROM : </td>
        <td width="75%" align="left">' . $proposal_info . '</td></tr></table></td>
        <td width="33.33%" align="left"><table width="100%" bgcolor="#fff">
        <tr><td width="25%" align="left">TO : </td>
        <td width="75%" align="left">' . $client_details . '</td></tr></table></td>
        <td width="33.33%" align="right">' . $proposalstatus . '</td>
    </tr>
    </table>';*/


//$pdf->writeHTML($proposalRow, true, false, false, false, '');

//$pdf->Ln(5);

// check for invoice custom fields which is checked show on pdf
/*$pdf_custom_fields = get_custom_fields('invoice', array(
    'show_on_pdf' => 1
));

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($proposal->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $pdf->writeHTMLCell(0, '', '', '', $field['name'] . ': ' . $value, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);
}*/

// The Table
/*$pdf->Ln(7);
$item_width = 38;*/
// If show item taxes is disabled in PDF we should increase the item width table heading
//$item_width = get_option('show_tax_per_item') == 0 ? $item_width + 15 : $item_width;

// Header
//$qty_heading = _l('invoice_table_quantity_heading');

/*$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th width="5%;" align="center">#</th>
        <th width="30%" align="left">' . _l('invoice_table_item_heading') . '</th>
        <th width="10%" align="right">' . $qty_heading . '</th>
        <th width="10%" align="right">' . _l('invoice_table_rate_heading') . '</th>
        <th width="20%" align="right">Markup/Discount</th>';

if (get_option('show_tax_per_item') == 1) {
    $tblhtml .= '<th width="10%" align="right">' . _l('invoice_table_tax_heading') . '</th>';
}

$tblhtml .= '<th width="15%" align="right">' . _l('invoice_table_amount_heading') . '</th>
</tr>';*/

// Items
//$tblhtml .= '<tbody>';

/*$items_data = get_table_items_and_taxes($proposal->items, 'invoice');

$tblhtml .= $items_data['html'];
$taxes = $items_data['taxes'];

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');*/

/*$pdf->Ln(8);
$tbltotal = '';

$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->proposal_subtotal) . '</td>
</tr>';

foreach ($taxes as $tax) {
    $total = array_sum($tax['total']);
    if ($proposal->discount_percent != 0 && $proposal->discount_type == 'before_tax') {
        $total_tax_calculated = ($total * $proposal->discount_percent) / 100;
        $total = ($total - $total_tax_calculated);
    }
    // The tax is in format TAXNAME|20
    $_tax_name = explode('|', $tax['tax_name']);
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . format_money($total, $proposal->symbol) . '</td>
</tr>';
}

if (isset($proposal->proposal)) {
    $proposal = $proposal->proposal;
    if (!empty($proposal->other) && $proposal->otherval > 0) {
        $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $proposal->other . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->otherval, $proposal->symbol) . '</td>
</tr>';
    }
}
$gratuity_percent = 0.00;
$gratuity_val = 0.00;

if ($gratuity_percent > 0 && $gratuity_val > 0) {
    $tbltotal .= '<tr>
                                <td align="right" width="85%"><strong>' . _l("Gratuity") . '(' . $gratuity_percent . '%)</strong></td><td align="right" width="15%" class="total">' . format_money($gratuity_val, $proposal->symbol) . '</td></tr>';
    $proposal->total = $proposal->total + $gratuity_val;
}
$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->proposal_total) . '</td>
</tr>';

if ($proposal->status == 3) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">' . format_money(sum_from_table('tblinvoicepaymentrecords', array(
            'field' => 'amount',
            'where' => array(
                'invoiceid' => $proposal->id
            )
        )), $proposal->symbol) . '</td>
    </tr>
    <tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . format_money(get_invoice_total_left_to_pay($proposal->id, $proposal->total), $proposal->symbol) . '</td>
   </tr>';
}

$tbltotal .= '</table>';

$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name), 0, 1, 'C', 0, '', 0);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}*/

/*if (found_invoice_mode($payment_modes, $proposal->id, true, true)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', 10);
    $pdf->Cell(0, 0, _l('invoice_html_offline_payment'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', 10);
    foreach ($payment_modes as $mode) {
        if (is_numeric($mode['id'])) {
            if (!is_payment_mode_allowed_for_invoice($mode['id'], $proposal->id)) {
                continue;
            }
        }
        if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
            $pdf->Ln(2);
            $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
            $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + $dimensions['rm']), 0, clear_textarea_breaks($mode['description']), 0, 'L');
        }
    }
}*/

/*if (!empty($proposal->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $proposal->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($proposal->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $proposal->terms, 0, 1, false, true, 'L', true);
}*/
