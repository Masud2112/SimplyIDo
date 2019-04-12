<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reports_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Leads conversions monthly report
     * @param   mixed $month  which month / chart
     * @return  array          chart data
     */
    public function leads_monthly_report($month)
    {
        $result      = $this->db->query('select last_status_change from tblleads where MONTH(last_status_change) = ' . $month . ' AND status = 1 and lost = 0')->result_array();
        $month_dates = array();
        $data        = array();
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, date('Y'));
            if (date('m', $time) == $month) {
                $month_dates[] = _d(date('Y-m-d', $time));
                $data[]        = 0;
            }
        }
        $chart = array(
            'labels' => $month_dates,
            'datasets' => array(
                array(
                    'label' => _l('leads'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor' => '#c53da9',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => $data
                )
            )
        );
        foreach ($result as $lead) {
            $i = 0;
            foreach ($chart['labels'] as $date) {
                if (_d($lead['last_status_change']) == $date) {
                    $chart['datasets'][0]['data'][$i]++;
                }
                $i++;
            }
        }

        return $chart;
    }

    public function get_stats_chart_data($label, $where, $dataset_options, $year)
    {
        $chart = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'label' => $label,
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array()
                )
            )
        );

        foreach ($dataset_options as $key => $val) {
            $chart['datasets'][0][$key] = $val;
        }
        $this->load->model('expenses_model');
        $categories = $this->expenses_model->get_category();
        foreach ($categories as $category) {
            $_where['category']   = $category['id'];
            $_where['YEAR(date)'] = $year;
            if (count($where) > 0) {
                foreach ($where as $key => $val) {
                    $_where[$key] = $val;
                }
            }
            array_push($chart['labels'], $category['name']);
            array_push($chart['datasets'][0]['data'], total_rows('tblexpenses', $_where));
        }

        return $chart;
    }

    public function get_expenses_vs_income_report($year = '')
    {
        $this->load->model('expenses_model');

        $months_labels  = array();
        $total_expenses = array();
        $total_income   = array();
        $i              = 0;
        if (!is_numeric($year)) {
            $year = date('Y');
        }
        for ($m = 1; $m <= 12; $m++) {
            array_push($months_labels, _l(date('F', mktime(0, 0, 0, $m, 1))));
            $this->db->select('id')->from('tblexpenses')->where('MONTH(date)', $m)->where('YEAR(date)', $year);
            $expenses = $this->db->get()->result_array();
            if (!isset($total_expenses[$i])) {
                $total_expenses[$i] = array();
            }
            if (count($expenses) > 0) {
                foreach ($expenses as $expense) {
                    $expense = $this->expenses_model->get($expense['id']);
                    $total = $expense->amount;
                    // Check if tax is applied
                    if ($expense->tax != 0) {
                        $total += ($total / 100 * $expense->taxrate);
                    }
                    if ($expense->tax2 != 0) {
                        $total += ($expense->amount / 100 * $expense->taxrate2);
                    }
                    $total_expenses[$i][] = $total;
                }
            } else {
                $total_expenses[$i][] = 0;
            }
            $total_expenses[$i] = array_sum($total_expenses[$i]);
            // Calculate the income
            $this->db->select('amount');
            $this->db->from('tblinvoicepaymentrecords');
            $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
            $this->db->where('MONTH(tblinvoicepaymentrecords.date)', $m);
            $this->db->where('YEAR(tblinvoicepaymentrecords.date)', $year);
            $payments = $this->db->get()->result_array();
            if (!isset($total_income[$m])) {
                $total_income[$i] = array();
            }
            if (count($payments) > 0) {
                foreach ($payments as $payment) {
                    $total_income[$i][] = $payment['amount'];
                }
            } else {
                $total_income[$i][] = 0;
            }
            $total_income[$i] = array_sum($total_income[$i]);
            $i++;
        }
        $chart = array(
            'labels' => $months_labels,
            'datasets' => array(
                array(
                    'label' => _l('report_sales_type_income'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => "#84c529",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => $total_income
                ),
                array(
                    'label' => _l('expenses'),
                    'backgroundColor' => 'rgba(252,45,66,0.4)',
                    'borderColor' => "#fc2d42",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => $total_expenses
                )
            )
        );

        return $chart;
    }

    /**
     * Chart leads weeekly report
     * @return array  chart data
     */
    public function leads_this_week_report()
    {
        $this->db->where('CAST(last_status_change as DATE) >= "' . date('Y-m-d', strtotime('monday this week')) . '" AND CAST(last_status_change as DATE) <= "' . date('Y-m-d', strtotime('sunday this week')) . '" AND status = 1 and lost = 0');
        $weekly = $this->db->get('tblleads')->result_array();
        $colors = get_system_favourite_colors();
        $chart  = array(
            'labels' => array(
                _l('wd_monday'),
                _l('wd_tuesday'),
                _l('wd_wednesday'),
                _l('wd_thursday'),
                _l('wd_friday'),
                _l('wd_saturday'),
                _l('wd_sunday')
            ),
            'datasets' => array(
                array(
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    ),
                    'backgroundColor' => array(
                        $colors[0],
                        $colors[1],
                        $colors[2],
                        $colors[3],
                        $colors[4],
                        $colors[5],
                        $colors[6]
                    ),
                    'hoverBackgroundColor' => array(
                        adjust_color_brightness($colors[0], -20),
                        adjust_color_brightness($colors[1], -20),
                        adjust_color_brightness($colors[2], -20),
                        adjust_color_brightness($colors[3], -20),
                        adjust_color_brightness($colors[4], -20),
                        adjust_color_brightness($colors[5], -20),
                        adjust_color_brightness($colors[6], -20)
                    )
                )
            )
        );
        foreach ($weekly as $weekly) {
            $lead_status_day = _l(mb_strtolower('wd_' . date('l', strtotime($weekly['last_status_change']))));
            $i               = 0;
            foreach ($chart['labels'] as $dat) {
                if ($lead_status_day == $dat) {
                    $chart['datasets'][0]['data'][$i]++;
                }
                $i++;
            }
        }

        return $chart;
    }

    public function leads_staff_report()
    {
        $this->load->model('staff_model');
        $staff = $this->staff_model->get();
        if ($this->input->post()) {
            $from_date = to_sql_date($this->input->post('staff_report_from_date'));
            $to_date   = to_sql_date($this->input->post('staff_report_to_date'));
        }
        $chart = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'label' => _l('leads_staff_report_created'),
                    'backgroundColor' => 'rgba(3,169,244,0.2)',
                    'borderColor' => "#03a9f4",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array()
                ),
                array(
                    'label' => _l('leads_staff_report_lost'),
                    'backgroundColor' => 'rgba(252,45,66,0.4)',
                    'borderColor' => "#fc2d42",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array()
                ),
                array(
                    'label' => _l('leads_staff_report_converted'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => "#84c529",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array()
                )
            )
        );
        foreach ($staff as $member) {
            array_push($chart['labels'], $member['firstname'] . ' ' . $member['lastname']);
            if (!isset($to_date) && !isset($from_date)) {
                $total_rows_converted = total_rows('tblleads', array(
                    'assigned' => $member['staffid'],
                    'status' => 1
                ));
                $total_rows_created   = total_rows('tblleads', array(
                    'addedfrom' => $member['staffid']
                ));
                $total_rows_lost      = total_rows('tblleads', array(
                    'assigned' => $member['staffid'],
                    'lost' => 1
                ));
            } else {
                $sql                  = "SELECT COUNT(tblleads.id) as total FROM tblleads WHERE DATE(last_status_change) BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND status = 1 AND assigned=" . $member['staffid'] . "";
                $total_rows_converted = $this->db->query($sql)->row()->total;

                $sql                = "SELECT COUNT(tblleads.id) as total FROM tblleads WHERE DATE(dateadded) BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND addedfrom=" . $member['staffid'] . "";
                $total_rows_created = $this->db->query($sql)->row()->total;

                $sql = "SELECT COUNT(tblleads.id) as total FROM tblleads WHERE DATE(last_status_change) BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND lost = 1 AND assigned=" . $member['staffid'] . "";

                $total_rows_lost = $this->db->query($sql)->row()->total;
            }

            array_push($chart['datasets'][0]['data'], $total_rows_created);
            array_push($chart['datasets'][1]['data'], $total_rows_lost);
            array_push($chart['datasets'][2]['data'], $total_rows_converted);
        }

        return $chart;
    }

    /**
     * Lead conversion by sources report / chart
     * @return arrray chart data
     */
    public function leads_sources_report()
    {
        $this->load->model('leads_model');
        $sources = $this->leads_model->get_source();
        $chart   = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'label' => _l('report_leads_sources_conversions'),
                    'backgroundColor' => 'rgba(124, 179, 66, 0.5)',
                    'borderColor' => '#7cb342',
                    'data' => array()
                )
            )
        );
        foreach ($sources as $source) {
            array_push($chart['labels'], $source['name']);
            array_push($chart['datasets'][0]['data'], total_rows('tblleads', array(
                'source' => $source['id'],
                'status' => 1,
                'lost' => 0
            )));
        }

        return $chart;
    }

    public function report_by_customer_groups()
    {
        $months_report = $this->input->post('months_report');
        $groups        = $this->clients_model->get_groups();
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
               // Last month
               if($months_report == '1'){
                   $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                   $endMonth   = date('Y-m-t', strtotime('-1 MONTH'));
               } else {
                   $months_report = (int) $months_report;
                   $months_report--;
                   $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                   $endMonth   = date('Y-m-t');
               }

                $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif($months_report == 'this_month'){
                $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif($months_report == 'this_year'){
                $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' .
                date('Y-m-d',strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d',strtotime(date('Y-12-'.date('d',strtotime('last day of this year'))))) . '")';
            } elseif($months_report == 'last_year'){
             $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' .
                date('Y-m-d',strtotime(date(date('Y',strtotime('last year')).'-01-01'))) .
                '" AND "' .
                date('Y-m-d',strtotime(date(date('Y',strtotime('last year')). '-12-'.date('d',strtotime('last day of last year'))))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'tblinvoicepaymentrecords.date ="' . $from_date . '"';
                } else {
                    $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
            $this->db->where($custom_date_select);
        }
        $this->db->select('amount,tblinvoicepaymentrecords.date,tblinvoices.clientid,(SELECT GROUP_CONCAT(name) FROM tblcustomersgroups LEFT JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblinvoices.clientid) as groups');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $this->db->where('tblinvoices.clientid IN (select customer_id FROM tblcustomergroups_in)');
        $this->db->where('tblinvoices.status !=', 5);
        $by_currency = $this->input->post('report_currency');
        if ($by_currency) {
            $this->db->where('currency', $by_currency);
        }
        $payments       = $this->db->get()->result_array();
        $data           = array();
        $data['temp']   = array();
        $data['total']  = array();
        $data['labels'] = array();
        foreach ($groups as $group) {
            if (!isset($data['groups'][$group['name']])) {
                $data['groups'][$group['name']] = $group['name'];
            }
        }
        // If any groups found
        if (isset($data['groups'])) {
            foreach ($data['groups'] as $group) {
                foreach ($payments as $payment) {
                    $p_groups = explode(',', $payment['groups']);
                    foreach ($p_groups as $p_group) {
                        if ($p_group == $group) {
                            $data['temp'][$group][] = $payment['amount'];
                        }
                    }
                }
                array_push($data['labels'], $group);
                if (isset($data['temp'][$group])) {
                    $data['total'][] = array_sum($data['temp'][$group]);
                }
            }
        }
        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => _l('customer_groups'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.2)',
                    'borderColor' => '#c53da9',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => $data['total']
                )
            )
        );

        return $chart;
    }

    public function report_by_payment_modes()
    {
        $this->load->model('payment_modes_model');
        $modes  = $this->payment_modes_model->get('', array(), true, true);
        $year   = $this->input->post('year');
        $colors = get_system_favourite_colors();
        $this->db->select('amount,tblinvoicepaymentrecords.date');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->where('YEAR(tblinvoicepaymentrecords.date)', $year);
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $by_currency = $this->input->post('report_currency');
        if ($by_currency) {
            $this->db->where('currency', $by_currency);
        }
        $all_payments   = $this->db->get()->result_array();
        $chart          = array(
            'labels' => array(),
            'datasets' => array()
        );
        $data           = array();
        $data['months'] = array();
        foreach ($all_payments as $payment) {
            $month   = date('m', strtotime($payment['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month   = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        usort($data['months'], function ($a, $b) {
            $month1 = date_parse($a);
            $month2 = date_parse($b);

            return $month1["month"] - $month2["month"];
        });

        foreach ($data['months'] as $month) {
            array_push($chart['labels'], _l($month) . ' - ' . $year);
        }
        $i = 0;
        foreach ($modes as $mode) {
            if (total_rows('tblinvoicepaymentrecords', array(
                'paymentmode' => $mode['id']
            )) == 0) {
                continue;
            }
            $color = '#4B5158';
            if (isset($colors[$i])) {
                $color = $colors[$i];
            }
            $this->db->select('amount,tblinvoicepaymentrecords.date');
            $this->db->from('tblinvoicepaymentrecords');
            $this->db->where('YEAR(tblinvoicepaymentrecords.date)', $year);
            $this->db->where('tblinvoicepaymentrecords.paymentmode', $mode['id']);
            $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $this->db->where('currency', $by_currency);
            }
            $payments = $this->db->get()->result_array();

            $datasets_data          = array();
            $datasets_data['total'] = array();
            foreach ($data['months'] as $month) {
                $total_payments = array();
                if (!isset($datasets_data['temp'][$month])) {
                    $datasets_data['temp'][$month] = array();
                }
                foreach ($payments as $payment) {
                    $_month  = date('m', strtotime($payment['date']));
                    $dateObj = DateTime::createFromFormat('!m', $_month);
                    $_month  = $dateObj->format('F');
                    if ($month == $_month) {
                        $total_payments[] = $payment['amount'];
                    }
                }
                $datasets_data['total'][] = array_sum($total_payments);
            }
            $chart['datasets'][] = array(
                'label' => $mode['name'],
                'backgroundColor' => $color,
                'borderColor' => adjust_color_brightness($color, -20),
                'tension' => false,
                'borderWidth' => 1,
                'data' => $datasets_data['total']
            );
            $i++;
        }

        return $chart;
    }

    /**
     * Total income report / chart
     * @return array chart data
     */
    public function total_income_report()
    {
        $year = $this->input->post('year');
        $this->db->select('amount,tblinvoicepaymentrecords.date');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->where('YEAR(tblinvoicepaymentrecords.date)', $year);
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $by_currency = $this->input->post('report_currency');
        if ($by_currency) {
            $this->db->where('currency', $by_currency);
        }
        $payments       = $this->db->get()->result_array();
        $data           = array();
        $data['months'] = array();
        $data['temp']   = array();
        $data['total']  = array();
        $data['labels'] = array();
        foreach ($payments as $payment) {
            $month   = date('m', strtotime($payment['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month   = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        usort($data['months'], function ($a, $b) {
            $month1 = date_parse($a);
            $month2 = date_parse($b);

            return $month1["month"] - $month2["month"];
        });
        foreach ($data['months'] as $month) {
            foreach ($payments as $payment) {
                $_month  = date('m', strtotime($payment['date']));
                $dateObj = DateTime::createFromFormat('!m', $_month);
                $_month  = $dateObj->format('F');
                if ($month == $_month) {
                    $data['temp'][$month][] = $payment['amount'];
                }
            }
            array_push($data['labels'], _l($month) . ' - ' . $year);
            $data['total'][] = array_sum($data['temp'][$month]);
        }
        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => _l('report_sales_type_income'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => "#84c529",
                    'tension' => false,
                    'borderWidth' => 1,
                    'data' => $data['total']
                )
            )
        );

        return $chart;
    }

    public function get_distinct_payments_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM tblinvoicepaymentrecords')->result_array();
    }

    public function get_distinct_customer_invoices_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM tblinvoices WHERE clientid=' . get_client_user_id())->result_array();
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/07/2018
    * for lead source reports
    */
    public function getleadsources_report($filter_data)
    {
        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        $leadsource_response = array();

        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblleads`.`dateadded`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblleads`.`dateadded`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblleads`.`dateadded`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblleads`.`dateadded`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblleads`.`dateadded`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN "' . $start_date . '" AND CURRENT_DATE()';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $responses = $this->db->query('SELECT `tblleadssources`.`id`, `tblleadssources`.`name`, GROUP_CONCAT(`tblleads`.`id`) AS leadid, COUNT(`tblleads`.`id`) AS no_of_leads, TRUNCATE((COUNT(`tblleads`.`id`)/(SELECT COUNT(l1.`id`) FROM `tblleads` l1 WHERE l1.`deleted` = 0 AND l1.`brandid` = ' . get_user_session() . ') * 100),2) AS lead_percent, (SELECT COUNT(`tblprojects`.`id`) FROM `tblprojects` WHERE `tblprojects`.`convert_type_id` > 0 AND `tblprojects`.`deleted` = 0 AND `tblprojects`.`parent` > 0) AS total_booking FROM `tblleadssources` LEFT JOIN `tblleads` ON `tblleadssources`.`id` = `tblleads`.`source` AND `tblleads`.`deleted` = 0 WHERE `tblleadssources`.`deleted` = 0 AND `tblleadssources`.`brandid` = ' . get_user_session() . $where . ' GROUP BY `tblleadssources`.`id`')->result_array();
        foreach ($responses as $response) {
            $temp_response = [];
            $temp_response['id']            = $response['id'];
            $temp_response['name']          = $response['name'];
            $temp_response['leadid']        = $response['leadid'];
            $temp_response['no_of_leads']   = $response['no_of_leads'];
            $temp_response['lead_percent']  = $response['lead_percent'];
            $temp_response['total_booking'] = $response['total_booking'];

            //get booking for each lead source
            if($response['leadid'] != "") {
                $booking_res = $this->db->query('SELECT IFNULL(COUNT(`tblprojects`.`id`),0) AS lead_booking FROM `tblprojects` WHERE `tblprojects`.`deleted` = 0 AND `tblprojects`.`parent` > 0 AND `tblprojects`.`convert_type_id` IN (' . $response['leadid'] . ')')->row();
                $temp_response['lead_booking']      = $booking_res->lead_booking;

                if($booking_res->lead_booking > 0) {
                    $temp_response['booking_percent']   = round(($booking_res->lead_booking / $response['total_booking']) * 100, 2);
                } else {
                    $temp_response['booking_percent']   = 0;
                }
            } else {
                $temp_response['booking_percent'] = 0;
            }

            //get invoice for each lead source
            if($response['leadid'] != "") {
                $total_invoice_res = $this->db->query('SELECT IFNULL(SUM(`tblinvoices`.`total`),0) AS total_value FROM `tblinvoices` WHERE `tblinvoices`.`leadid` IN (' . $response['leadid'] . ')')->row();
                if($total_invoice_res->total_value > 0) {
                    $temp_response['total_value']   = $total_invoice_res->total_value;
                } else {
                    $temp_response['total_value']   = 0;
                }
            } else {
                $temp_response['total_value']   = 0;
            }

            //get average invoice for each lead source
            if($response['leadid'] != "") {
                $avg_invoice_res = $this->db->query('SELECT IFNULL(SUM(`tblinvoices`.`total`),0) AS avg_value FROM `tblinvoices` WHERE `tblinvoices`.`leadid` IN (' . $response['leadid'] . ')')->row();
                if($avg_invoice_res->avg_value > 0) {
                    $temp_response['avg_value']   = round($avg_invoice_res->avg_value / $response['no_of_leads'],2);
                } else {
                    $lead_invoice_res = $this->db->query('SELECT IFNULL(SUM(`tblleads`.`budget`),0) AS avg_value FROM `tblleads` WHERE `tblleads`.`id` IN (' . $response['leadid'] . ')')->row();
                    $temp_response['avg_value']   = round($lead_invoice_res->avg_value / $response['no_of_leads'],2);
                }
            } else {
                $temp_response['avg_value']   = 0;
            }

            //get average time to project
            if($response['leadid'] != "") {
                $avg_time_to_project_res = $this->db->query('SELECT IFNULL(SUM(DATEDIFF(DATE(`tblleads`.`eventstartdatetime`), DATE(`eventinquireon`))),0) AS avg_time_to_project FROM `tblleads` WHERE `tblleads`.`id` IN (' . $response['leadid'] . ')')->row();
                if($avg_time_to_project_res->avg_time_to_project > 0 && $response['total_booking'] > 0) {
                    $temp_response['avg_time_to_project']   = floor($avg_time_to_project_res->avg_time_to_project / $response['total_booking']);
                } else {
                    $temp_response['avg_time_to_project']   = 0;
                }
            } else {
                $temp_response['avg_time_to_project']   = 0;
            }

            //get average time to booking
            if($response['leadid'] != "") {
               $avg_time_to_booking_res = $this->db->query('SELECT IFNULL(SUM(DATEDIFF(DATE(`tblprojects`.`datecreated`), DATE(`eventinquireon`))),0) AS avg_time_to_booking FROM `tblprojects` JOIN `tblleads` ON `tblleads`.`id` = `tblprojects`.`convert_type_id` WHERE `tblprojects`.`deleted` = 0 AND `tblleads`.`source` = '. $temp_response['id'])->row();
                if($avg_time_to_booking_res->avg_time_to_booking > 0) {
                    $temp_response['avg_time_to_booking']   = floor($avg_time_to_booking_res->avg_time_to_booking / $response['total_booking']);
                } else {
                    $temp_response['avg_time_to_booking']   = 0;
                }
            } else {
                $temp_response['avg_time_to_booking']   = 0;
            }

            array_push($leadsource_response, $temp_response);
        }
        
        return $leadsource_response;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/08/2018
    * for lead status reports
    */
    public function getleadstatus_report($filter_data)
    {
        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        //$default_records    = $filter_data['default_records'];
        $leadsource_response = array();

        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblleads`.`dateadded`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblleads`.`dateadded`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblleads`.`dateadded`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblleads`.`dateadded`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblleads`.`dateadded`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblleads`.`dateadded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblleads`.`dateadded`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $leadstatus_response = array();
        $this->db->query('SET sql_mode=""');
        //$responses = $this->db->query('SELECT `tblleadsstatus`.`id`, `tblleadsstatus`.`name`, GROUP_CONCAT(`tblleads`.`id`) AS leadid, COUNT(`tblleads`.`id`) AS no_of_leads, (SELECT COUNT(`tblleads`.`id`) FROM `tblleads` WHERE `tblleads`.`deleted` = 0 AND `tblleads`.`converted` = 1 AND `tblleads`.`status` = `tblleadsstatus`.`id`) AS total_booking, TRUNCATE((((SELECT COUNT(`tblleads`.`id`) FROM `tblleads` WHERE `tblleads`.`deleted` = 0 AND `tblleads`.`converted` = 1 AND `tblleads`.`status` = `tblleadsstatus`.`id`)/ COUNT(`tblleads`.`id`)) * 100),2) AS booking_percent  FROM `tblleadsstatus` LEFT JOIN `tblleads` ON `tblleadsstatus`.`id` = `tblleads`.`status` WHERE `tblleadsstatus`.`deleted` = 0 AND `tblleadsstatus`.`brandid` = ' . get_user_session() . $where . ' GROUP BY `tblleadsstatus`.`id`')->result_array();
        $responses = $this->db->query('SELECT `tblleadsstatus`.`id`, `tblleadsstatus`.`name`, GROUP_CONCAT(`tblleads`.`id`) AS leadid, COUNT(`tblleads`.`id`) AS no_of_leads, (SELECT COUNT(`tblleads`.`id`) FROM `tblleads` WHERE `tblleads`.`deleted` = 0 AND `tblleads`.`converted` = 1 AND `tblleads`.`status` = `tblleadsstatus`.`id`) AS total_booking, TRUNCATE((((SELECT COUNT(`tblleads`.`id`) FROM `tblleads` WHERE `tblleads`.`deleted` = 0 AND `tblleads`.`converted` = 1 AND `tblleads`.`status` = `tblleadsstatus`.`id`)/ COUNT(`tblleads`.`id`)) * 100),2) AS booking_percent  FROM `tblleadsstatus` LEFT JOIN `tblleadstatushistory` ON `tblleadstatushistory`.`new_statusid` = `tblleadsstatus`.`id` LEFT JOIN `tblleads` ON `tblleadstatushistory`.`leadid` = `tblleads`.`id` WHERE `tblleadsstatus`.`deleted` = 0 AND `tblleadsstatus`.`brandid` = ' . get_user_session() . $where . ' GROUP BY `tblleadsstatus`.`id`')->result_array();
        foreach ($responses as $response) {
            $temp_response = [];
            $temp_response['id']                = $response['id'];
            $temp_response['name']              = $response['name'];
            $temp_response['leadid']            = $response['leadid'];
            $temp_response['no_of_leads']       = $response['no_of_leads'];
            $temp_response['booking_percent']   = $response['booking_percent'];
            $temp_response['total_booking']     = $response['total_booking'];

            //get average time to status
            if($response['leadid'] != "") {
               $avg_time_to_booking_res = $this->db->query('SELECT IFNULL(SUM(DATEDIFF(DATE(`tblprojects`.`datecreated`), DATE(`eventinquireon`))),0) AS avg_time_to_booking FROM `tblprojects` JOIN `tblleads` ON `tblleads`.`id` = `tblprojects`.`convert_type_id` WHERE `tblprojects`.`deleted` = 0 AND `tblleads`.`status` = '. $temp_response['id'])->row();
                if($avg_time_to_booking_res->avg_time_to_booking > 0) {
                    $temp_response['avg_time_to_booking']   = floor($avg_time_to_booking_res->avg_time_to_booking / $response['total_booking']);
                } else {
                    $temp_response['avg_time_to_booking']   = 0;
                }
            } else {
                $temp_response['avg_time_to_booking']   = 0;
            }

            array_push($leadstatus_response, $temp_response);
        }
        
        return $leadstatus_response;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/08/2018
    * for revenue reports
    */
    public function getrevenues_report($filter_data)
    {
        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        $leadsource_response = array();

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        
        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";
        
        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblinvoices`.`created_date`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblinvoices`.`created_date`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblinvoices`.`created_date`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblinvoices`.`created_date`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblinvoices`.`created_date`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblinvoices`.`created_date`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblinvoices`.`created_date`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblinvoices`.`created_date`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblinvoices`.`created_date`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblinvoices`.`created_date`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblinvoices`.`created_date`) BETWEEN "' . $start_date . '" AND "' . $end_date . '"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblinvoices`.`created_date`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $total_invoice = $this->db->query('SELECT IFNULL(COUNT(`tblinvoices`.`id`),0) AS cnt_invoice FROM `tblinvoices` WHERE `tblinvoices`.`brandid` = ' . get_user_session())->row();
  
        $revenue_responses = $this->db->query('SELECT `tblinvoices`.`status`,IFNULL(COUNT(`tblinvoices`.`id`),0) AS no_of_revenues, TRUNCATE((IFNULL(COUNT(`tblinvoices`.`id`),0)/ ' . $total_invoice->cnt_invoice. ')*100,2) AS revenue_percent, IFNULL(SUM(`tblinvoices`.`total`),0) AS total_revenue FROM `tblinvoices` WHERE `tblinvoices`.`brandid` = ' . get_user_session() . $where . ' GROUP BY `tblinvoices`.`status`')->result_array();
        
        return $revenue_responses;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/13/2018
    * for report configuration
    */
    public function get_configuration($name = '', $id = '')
    {        
        if(!empty(get_user_session())) {
            $this->db->where('brandid', get_user_session());
        }

        if(!empty(get_staff_user_id())) {
            $this->db->where('staff_user_id', get_staff_user_id());
        }

        if(!empty($name)) {
            $this->db->where('report_name', $name);
            return $this->db->get('tblreportconfiguration')->row();
        }

        if(!empty($id)) {
            $this->db->where('reportconfigurationid', $id);
            return $this->db->get('tblreportconfiguration')->row();
        }
        
        $this->db->order_by('report_order','asc');
        return $this->db->get('tblreportconfiguration')->result_array();
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/13/2018
    * to update configuration
    */
    public function update_configuration($data) {
        $config_data = [];

        $reportconfigurationid = $data['reportconfigurationid'];

        if(isset($data['type']) && $data['type'] == 'is_visible') {
            $type = 'is_visible';
            $report = $this->get_configuration('', $reportconfigurationid);

            if($report->is_visible == 1) {
                $config_data['is_visible'] = 0;
            } else {
                $config_data['is_visible'] = 1;
            }

            if(isset($type)) {
                unset($data['type']);
            }
        }

        if(isset($data['report_order'])) {
            $config_data['report_order']  = $data['report_order'];
        }

        if(isset($data['saved_filter'])) {
            $config_data['saved_filter']  = $data['saved_filter'];
        }

        if(isset($data['sharing_permission'])) {
            if(is_array($data['sharing_permission'])) {
                $config_data['sharing_permission'] = implode(",", $data['sharing_permission']);
            } else {
                $config_data['sharing_permission'] = $data['sharing_permission'];
            }

            //get report name
            $reportname_row = $this->db->query('SELECT `report_name` FROM `tblreportconfiguration` WHERE `reportconfigurationid` = ' . $data['reportconfigurationid'])->row();

            //if report permission exists, update else insert
            $this->db->where('brandid', get_user_session());
            $this->db->where('report_name', $reportname_row->report_name);
            $exists = $this->db->get('tblreportpermission')->row();
          
            if(empty($exists->reportpermissionid)) {
                $permission_data = [];
                $permission_data['brandid']                 = get_user_session();
                $permission_data['sharing_permission']      = $config_data['sharing_permission'];
                $permission_data['report_name']             = $reportname_row->report_name;
                $permission_data['createdby']               = get_staff_user_id();
                $permission_data['datecreated']             = date('Y-m-d H:i:s');

                $this->db->insert('tblreportpermission', $permission_data);                
            } else {
                $permission_data = [];
                $permission_data['sharing_permission']      = $config_data['sharing_permission'];
                $permission_data['updatedby']               = get_staff_user_id();
                $permission_data['dateupdated']             = date('Y-m-d H:i:s');

                $this->db->where('brandid', get_user_session());
                $this->db->where('report_name', $reportname_row->report_name);
                $this->db->update('tblreportpermission', $permission_data);
            }
        }

        if(isset($data['default_records'])) {
            $config_data['default_records'] = $data['default_records'];
        }

        if(isset($data['start_date'])) {
            $config_data['start_date'] = $data['start_date'];
        }

        if(isset($data['end_date'])) {
            $config_data['end_date'] = $data['end_date'];
        }

        if(isset($data['start_date']) && isset($data['end_date'])) {
            $this->db->where('start_date', $data['start_date']);
            $this->db->where('end_date', $data['end_date']);
            $this->db->where('reportname', $reportconfigurationid);
            $exists = $this->db->get('tblreportfilters')->row();

            if(!$exists) {
                $filter_data = [];
                $filter_data['filtername']      = "Custom".$data['start_date'] . " - " . $data['end_date'];
                $filter_data['filtervalue']     = $data['start_date'] . " to " . $data['end_date'];
                $filter_data['staff_user_id']   = get_staff_user_id();
                $filter_data['brandid']         = get_user_session();
                $filter_data['reportname']      = $reportconfigurationid;
                $filter_data['isdefault']       = 0;
                $filter_data['start_date']      = $data['start_date'];
                $filter_data['end_date']        = $data['start_date'];
                $filter_data['createdby']       = get_staff_user_id();
                $filter_data['datecreated']     = date('Y-m-d H:i:s');

                $this->db->insert('tblreportfilters', $filter_data);
            }
        }

        unset($data['reportconfigurationid']);

        //store each record
        $config_data['updatedby']           = get_staff_user_id();
        $config_data['dateupdated']         = date('Y-m-d H:i:s');

        if(!empty(get_staff_user_id())) {
            $this->db->where('staff_user_id', get_staff_user_id());
        }

        if(!empty(get_user_session())) {
            $this->db->where('brandid', get_user_session());
        }

        $this->db->where('reportconfigurationid', $reportconfigurationid);
        $this->db->update('tblreportconfiguration', $config_data);
        
        if($this->db->affected_rows() > 0) {
            if(isset($type) && $type == 'is_visible' && $data['is_visible'] == 1) {
                return "show";
            } elseif (isset($type) && $type == 'is_visible' && $data['is_visible']  == 0) {
                return "hide";
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/17/2018
    * to get report filter
    */
    public function get_filters($reportid = '') {
        $this->db->where('isdefault', 1);
        
        if(!empty($reportid) || $reportid != '') {
            $this->db->or_where('reportname', $reportid);
        }

        return $this->db->get('tblreportfilters')->result_array();
    }

    /**
    * Added By: Vaidehi
    * Dt: 04/22/2018
    * to get net revenue report filter
    */
    public function get_netrevenuefilters($reportid = '') {
        $this->db->where('isdefault', 1);
        $this->db->where('is_visible_netrevenue', 1);
        
        if(!empty($reportid) || $reportid != '') {
            $this->db->or_where('reportname', $reportid);
        }

        return $this->db->get('tblreportfilters')->result_array();
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/21/2018
    * to get report sharing permission
    */
    public function get_permission($reportname) {
        $this->db->select('sharing_permission');
        $this->db->where('brandid', get_user_session());
        $this->db->where('report_name', $reportname);
        return $this->db->get('tblreportpermission')->row();
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/22/2018
    * for signup reports
    */
    public function getsignup_report($filter_data)
    {

        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }
        //$default_records    = $filter_data['default_records'];
        $signup_response = array();

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }

        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblbrand`.`datecreated`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblbrand`.`datecreated`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblbrand`.`datecreated`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblbrand`.`datecreated`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $this->db->query('SET sql_mode=""');
       
        $responses = $this->db->query('SELECT `tblbrandtype`.`name`, IFNULL(COUNT(`tblbrand`.`brandid`),0) AS signups, TRUNCATE(((IFNULL(COUNT(`tblbrand`.`brandid`),0) )/(SELECT IFNULL(COUNT(b1.`brandid`),0) FROM `tblbrandtype` bt1 LEFT JOIN `tblbrand` b1 ON bt1.`brandtypeid` = b1.`brandtypeid` LEFT JOIN `tblclients` c1 ON b1.`userid` = c1.`userid` WHERE b1.`deleted` = 0 AND c1.`is_deleted` = 0 AND c1.`active` = 1))*100,2) AS signup_percent FROM `tblbrandtype` LEFT JOIN `tblbrand` ON `tblbrandtype`.`brandtypeid` = `tblbrand`.`brandtypeid` LEFT JOIN `tblclients` ON `tblbrand`.`userid` = `tblclients`.`userid` WHERE `tblbrand`.`deleted` = 0 AND `tblclients`.`is_deleted` = 0 AND `tblclients`.`active` = 1 ' . $where . ' GROUP BY `tblbrandtype`.`brandtypeid`')->result_array();
        
        return $responses;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/22/2018
    * for subscriber reports
    */
    public function getsubscriber_report($filter_data)
    {

        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        //$default_records    = $filter_data['default_records'];
        $signup_response = array();

        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`daterecorded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`daterecorded`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`daterecorded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        }  else {
            $where = " AND 1 = 1";
        }

        $this->db->query('SET sql_mode=""');
        
        $subscriber_response = array();
        $responses = $this->db->query('SELECT `tblbrandtype`.`name`, IFNULL(COUNT(`tblbrand`.`brandid`),0) AS subscribers, TRUNCATE(((IFNULL(COUNT(`tblbrand`.`brandid`),0) )/(SELECT IFNULL(COUNT(b1.`brandid`),0) FROM `tblbrandtype` bt1 JOIN `tblbrand`b1 ON bt1.`brandtypeid` = b1.`brandtypeid` JOIN `tblclients` c1 ON b1.`userid` = c1.`userid`  JOIN `tblsubscriptionpaymentrecords` s1 ON s1.`userid` = c1.`userid` JOIN `tblpackages` p1 ON s1.`packageid` = p1.`packageid` JOIN `tblpackagetype` pt1 ON pt1.`id` = p1.`packagetypeid` WHERE b1.`deleted` = 0  AND c1.`is_deleted` = 0 AND c1.`active` = 1 AND pt1.`name` != "Trial"))*100,2) AS subscriber_percent, IFNULL(SUM(DATEDIFF(DATE(`tblsubscriptionpaymentrecords`.`daterecorded`), DATE(`tblbrand`.`datecreated`))),0) AS avg_time FROM `tblbrandtype` JOIN `tblbrand` ON `tblbrandtype`.`brandtypeid` = `tblbrand`.`brandtypeid` JOIN `tblclients` ON `tblbrand`.`userid` = `tblclients`.`userid`  JOIN `tblsubscriptionpaymentrecords` ON `tblsubscriptionpaymentrecords`.`userid` = `tblclients`.`userid` JOIN `tblpackages` ON `tblsubscriptionpaymentrecords`.`packageid` = `tblpackages`.`packageid` JOIN `tblpackagetype` ON `tblpackagetype`.`id` = `tblpackages`.`packagetypeid` WHERE `tblbrand`.`deleted` = 0  AND `tblclients`.`is_deleted` = 0 AND `tblclients`.`active` = 1 AND `tblpackagetype`.`name` != "Trial"' . $where . ' GROUP BY `tblbrandtype`.`brandtypeid`')->result_array();
        foreach ($responses as $response) {
            $temp_response = [];
            $temp_response['name']                  = $response['name'];
            $temp_response['subscribers']           = $response['subscribers'];
            $temp_response['subscriber_percent']    = $response['subscriber_percent'];
        
            //get average time to paid
            if($response['avg_time'] > 0) {
                $temp_response['avg_time_to_paid']   = floor($response['avg_time'] / $response['subscribers']);
            } else {
                $temp_response['avg_time_to_paid']   = 0;
            }

            array_push($subscriber_response, $temp_response);
        }
        
        return $subscriber_response;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/22/2018
    * for conversion rate reports
    */
    public function getconversionrate_report($filter_data)
    {

        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        //$default_records    = $filter_data['default_records'];
        $signup_response = array();

        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`daterecorded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`daterecorded`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblsubscriptionpaymentrecords`.`dateadded`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblsubscriptionpaymentrecords`.`daterecorded`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $this->db->query('SET sql_mode=""');
        
        $conversionrate_response = array();
        $responses = $this->db->query('SELECT `tblbrandtype`.`name`, IFNULL(COUNT(`tblbrand`.`brandid`),0) AS signups FROM `tblbrandtype` LEFT JOIN `tblbrand` ON `tblbrandtype`.`brandtypeid` = `tblbrand`.`brandtypeid` LEFT JOIN `tblclients` ON `tblbrand`.`userid` = `tblclients`.`userid` LEFT JOIN `tblpackages` ON `tblpackages`.`packageid` = `tblclients`.`packageid` LEFT JOIN `tblpackagetype` ON `tblpackagetype`.`id` = `tblpackages`.`packagetypeid` WHERE `tblbrand`.`deleted` = 0 AND `tblclients`.`is_deleted` = 0 AND `tblclients`.`active` = 1 AND `tblpackagetype`.`name` != "Paid"' . $where . ' GROUP BY `tblbrandtype`.`brandtypeid`')->result_array();
        foreach ($responses as $response) {
            $temp_response = [];
            $temp_response['name']                  = $response['name'];
            $temp_response['signups']               = $response['signups'];
            
            $num_subscribers = $this->db->query('SELECT IFNULL(COUNT(`tblbrand`.`brandid`),0) AS subscribers FROM `tblbrandtype` LEFT JOIN `tblbrand` ON `tblbrandtype`.`brandtypeid` = `tblbrand`.`brandtypeid` LEFT JOIN `tblclients` ON `tblbrand`.`userid` = `tblclients`.`userid` JOIN `tblsubscriptionpaymentrecords` ON `tblsubscriptionpaymentrecords`.`userid` = `tblclients`.`userid` JOIN `tblpackages` ON `tblclients`.`packageid` = `tblpackages`.`packageid` JOIN `tblpackagetype` ON `tblpackagetype`.`id` = `tblpackages`.`packagetypeid` WHERE `tblbrand`.`deleted` = 0 AND `tblclients`.`is_deleted` = 0 AND `tblclients`.`active` = 1 AND `tblpackagetype`.`name` = "Paid" AND `tblsubscriptionpaymentrecords`.`amount` > 0 AND `tblbrandtype`.`name` = "' . $response['name'] . '" ' . $where)->row();

            $temp_response['subscribers']               = $num_subscribers->subscribers;
            if($num_subscribers->subscribers > 0) {
                $temp_response['conversion']        = floor(($response['signups'] / $num_subscribers->subscribers) * 100);
            } else {
                $temp_response['conversion']        = 0;    
            }

            array_push($conversionrate_response, $temp_response);
        }
        
        return $conversionrate_response;
    }

    /**
    * Added By: Vaidehi
    * Dt: 04/06/2018
    * for churn reports
    */
    public function getchurn_report($filter_data)
    {

        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        //$default_records    = $filter_data['default_records'];
        $churn_response = array();

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        
        $success = $this->update_configuration($filter_data);
        
        $where = " AND 1 = 1";

        //filter values
        if($filter == "today") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) = CURRENT_DATE()';
        } elseif($filter == "this_week") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE()';
        } elseif($filter == "this_month") {
            $where = ' AND MONTH(`tblbrand`.`datecreated`) = MONTH(CURRENT_DATE())';
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblbrand`.`datecreated`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
        } elseif($filter == "this_year") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "last_week") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)';
        } elseif($filter == "last_month") {
            $where = ' AND MONTH(`tblbrand`.`datecreated`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $where = ' AND MONTH(`tblbrand`.`datecreated`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
        } elseif($filter == "last_year") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
        } elseif($filter == "this_year_to_date") {
            $where = ' AND YEAR(`tblbrand`.`datecreated`) = YEAR(CURRENT_DATE())';
        } elseif($filter == "custom") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
        } elseif($filter == "custom_search") {
            $where = ' AND DATE(`tblbrand`.`datecreated`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
        } else {
            $where = " AND 1 = 1";
        }

        $this->db->query('SET sql_mode=""');
       
        $responses = $this->db->query('SELECT `tblbrandtype`.`name`, IFNULL(COUNT(`tblbrand`.`brandid`),0) AS churns, TRUNCATE(((IFNULL(COUNT(`tblbrand`.`brandid`),0) )/(SELECT IFNULL(COUNT(b1.`brandid`),0)  FROM `tblbrandtype` bt1 JOIN `tblbrand` b1 ON bt1.`brandtypeid` = b1.`brandtypeid` JOIN `tblclients` c1 ON b1.`userid` = c1.`userid`  JOIN `tblsubscriptionpaymentrecords` s1 ON s1.`userid` = c1.`userid` JOIN `tblpackages` p1 ON s1.`packageid` = p1.`packageid` JOIN `tblpackagetype` pt1 ON pt1.`id` = p1.`packagetypeid` WHERE b1.`deleted` = 0  AND c1.`is_deleted` = 0 AND c1.`active` = 1 AND pt1.`name` = "Paid" AND s1.`iscancel` = 1))*100,2) AS churn_percent, IFNULL(SUM(DATEDIFF(DATE(`tblsubscriptionpaymentrecords`.`daterecorded`), DATE(`tblbrand`.`datecreated`))),0) AS avg_time FROM `tblbrandtype` JOIN `tblbrand` ON `tblbrandtype`.`brandtypeid` = `tblbrand`.`brandtypeid` JOIN `tblclients` ON `tblbrand`.`userid` = `tblclients`.`userid`  JOIN `tblsubscriptionpaymentrecords` ON `tblsubscriptionpaymentrecords`.`userid` = `tblclients`.`userid` JOIN `tblpackages` ON `tblsubscriptionpaymentrecords`.`packageid` = `tblpackages`.`packageid` JOIN `tblpackagetype` ON `tblpackagetype`.`id` = `tblpackages`.`packagetypeid` WHERE `tblbrand`.`deleted` = 0  AND `tblclients`.`is_deleted` = 0 AND `tblclients`.`active` = 1 AND `tblpackagetype`.`name` = "Paid" AND `tblsubscriptionpaymentrecords`.`iscancel` = 1 ' . $where . ' GROUP BY `tblbrandtype`.`brandtypeid`')->result_array();
        foreach ($responses as $response) {
            $temp_response = [];
            $temp_response['name']          = $response['name'];
            $temp_response['churns']        = $response['churns'];
            $temp_response['churn_percent'] = $response['churn_percent'];
        
            //get average time to paid
            if($response['avg_time'] > 0) {
                $temp_response['avg_time_to_cancel']    = floor($response['avg_time'] / $response['churns']);
            } else {
                $temp_response['avg_time_to_cancel']    = 0;
            }

            array_push($churn_response, $temp_response);
        }

        return $churn_response;
    }

    /**
    * Added By: Vaidehi
    * Dt: 04/06/2018
    * for net revenue reports
    */
    public function getnetrevenue_report($filter_data)
    {
        if(isset($filter_data['startDate'])) {
            $sdate = explode("GMT", $filter_data['startDate']);
            $start_date = date('Y/m/d',strtotime($sdate[0]));
            unset($filter_data['startDate']);
            $filter_data['start_date']  = $start_date;
        } else if(isset($filter_data['start_date'])) {
            $start_date = $filter_data['start_date'];
        }

        if(isset($filter_data['endDate'])) {
            $edate = explode("GMT", $filter_data['endDate']);
            $end_date = date('Y/m/d',strtotime($edate[0]));
            unset($filter_data['endDate']);
            $filter_data['end_date']  = $end_date;
        } else if(isset($filter_data['end_date'])) {
            $end_date = $filter_data['end_date'];
        }

        $filter             = $filter_data['saved_filter'];

        if(strpos($filter_data['saved_filter'], ' to ') !== false && $filter_data['saved_filter'] != 'custom') {
            $filter = 'custom_search';
            $dates_search = explode(" to ", $filter_data['saved_filter']);
        }

        //$default_records    = $filter_data['default_records'];

        if($filter == 'all' || $filter == 'today' || $filter == 'this_week' || $filter == 'this_month' || $filter == 'this_quarter' || $filter == 'this_year' || $filter == 'last_week' || $filter == 'last_month' || $filter == 'last_quarter' || $filter == 'last_year' || $filter == 'this_year_to_date' ) {
            unset($filter_data['start_date']);
            unset($filter_data['end_date']);
        }
        $filter_data['is_visible_netrevenue'] = 1;
        $success = $this->update_configuration($filter_data);

        $this->db->query('SET sql_mode=""');

        if($filter == "this_month") {
            $get_month = $this->db->query('SELECT MONTHNAME(CURRENT_DATE()) AS current_month')->row();
            $monthname = $get_month->current_month;
            $months = array($monthname);
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `monthname` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $months = $quarter_value->monthname;
        } elseif($filter == "last_month") {
            $get_month = $this->db->query('SELECT MONTHNAME(CURRENT_DATE() - INTERVAL 1 MONTH) AS current_month')->row();
            $monthname = $get_month->current_month;
            $months = array($monthname);
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastmonthname` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $months = $quarter_value->lastmonthname;
        } else {
            $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        }

        if($filter == "this_month") {
            $get_month = $this->db->query('SELECT MONTHNAME(CURRENT_DATE() - INTERVAL 1 MONTH) AS current_month')->row();
            $monthname = $get_month->current_month;
            $churn_month = array($monthname);
        } elseif($filter == "this_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `churnmonthname` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $churn_month = $quarter_value->churnmonthname;
        } elseif($filter == "last_month") {
            $get_month = $this->db->query('SELECT MONTHNAME(CURRENT_DATE() - INTERVAL 2 MONTH) AS current_month')->row();
            $monthname = $get_month->current_month;
            $churn_month = array($monthname);
        } elseif($filter == "last_quarter") {
            $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
            $quarter_value = $this->db->query('SELECT `lastchurnmonthname` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
            $churn_month = $quarter_value->lastchurnmonthname;
        } else {
            $churn_month = array('December', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November');
        }
        
        $netrevenue_response = array();
        
        if(gettype($months) == 'string') {
            $months = explode(",", $months);
        }

        if(gettype($churn_month) == 'string') {
            $churn_month = explode(",", $churn_month);
        }

        foreach ($months as $key => $month) {
            $where = " AND 1 = 1";

            //filter values
            if($filter == "this_month") {
                $where = ' AND MONTHNAME(`daterecorded`) = MONTH(CURRENT_DATE())';
            } elseif($filter == "this_quarter") {
                $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
                $quarter_value = $this->db->query('SELECT `thisquarterstartvalue`, `thisquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
                $where = ' AND MONTHNAME(`daterecorded`) BETWEEN ' . $quarter_value->thisquarterstartvalue . ' AND ' . $quarter_value->thisquarterendvalue;
            } elseif($filter == "this_year") {
                $where = ' AND YEAR(`daterecorded`) = YEAR(CURRENT_DATE())';
            } elseif($filter == "last_month") {
                $where = ' AND MONTH(`daterecorded`) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)';
            } elseif($filter == "last_quarter") {
                $quarter_name = $this->db->query('SELECT `quartername` FROM `tblreportquarter` WHERE `quartermonth` = MONTH(CURRENT_DATE())')->row();
                $quarter_value = $this->db->query('SELECT `lastquarterstartvalue`, `lastquarterendvalue` FROM `tblreportquarterfilter` WHERE `quartername` = "' . $quarter_name->quartername . '"')->row();
                $where = ' AND MONTH(`daterecorded`) BETWEEN ' . $quarter_value->lastquarterstartvalue . ' AND ' . $quarter_value->lastquarterendvalue;
            } elseif($filter == "last_year") {
                $where = ' AND YEAR(`daterecorded`) = YEAR(CURRENT_DATE() - INTERVAL 1 YEAR)';
            } elseif($filter == "this_year_to_date") {
                $where = ' AND YEAR(`daterecorded`) = YEAR(CURRENT_DATE())';
            } elseif($filter == "custom") {
                $where = ' AND DATE(`daterecorded`) BETWEEN "' . $start_date . '" AND "'.$end_date.'"';
            } elseif($filter == "custom_search") {
                $where = ' AND DATE(`daterecorded`) BETWEEN "' . $dates_search[0] . '" AND "'.$dates_search[1].'"';
            } else {
                $where = " AND 1 = 1";
            }

            $revenue_row = $this->db->query('SELECT IFNULL(SUM(`amount`),0) AS amount FROM `tblsubscriptionpaymentrecords` WHERE `iscancel` != 1 AND MONTHNAME(`daterecorded`) = "' . $month . '"')->row();

            $churn_row = $this->db->query('SELECT IFNULL(SUM(`amount`),0) AS amount FROM `tblsubscriptionpaymentrecords` WHERE `iscancel` = 1 AND MONTHNAME(`daterecorded`) = "' . $churn_month[$key] . '"')->row();

            $temp_response              = [];
            $temp_response['month']     = $month;
            $temp_response['revenue']   = $revenue_row->amount;
            $temp_response['churn']     = $churn_row->amount;
            $temp_response['net']       = $revenue_row->amount - $churn_row->amount;
            
            array_push($netrevenue_response, $temp_response);
        }

        return $netrevenue_response;
    }
}
