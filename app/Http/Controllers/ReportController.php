<?php

namespace App\Http\Controllers;

use App\Models\CustomerRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $cashiers = CustomerRole::with('userByRole', 'userByRole.user')->where([
            'as_role' => 'cashier',
            'user_id' => session('userLogged')['company']['user_id'],
        ])->first();
        $reportTemplates = [
            [
                'name' => 'Sales Summary',
                'template' => 'sales.sales-summary',
                'cashier' => false,
                'description' => 'Summary of sales grouped by day, week, or month.',
            ],
            [
                'name' => 'Sales by Product',
                'template' => 'sales.sales-by-product',
                'cashier' => false,
                'description' => 'Detailed report of sales per product.',
            ],
            [
                'name' => 'Sales by Category',
                'template' => 'sales.sales-by-category',
                'cashier' => false,
                'description' => 'Sales report grouped by product categories.',
            ],
            [
                'name' => 'Sales by Cashier',
                'template' => 'sales.sales-by-cashier',
                'cashier' => true,
                'description' => 'Displays sales made by each cashier or staff member.',
            ],
            [
                'name' => 'Stock On Hand',
                'template' => 'product.stock-on-hand',
                'cashier' => false,
                'description' => 'Current available stock quantities for each product.',
            ],
            [
                'name' => 'Stock Movement Report',
                'template' => 'product.stock-movements',
                'cashier' => false,
                'description' => 'Report of all stock movements: stock in, stock out, and adjustments.',
            ],
            [
                'name' => 'Stock Opname',
                'template' => 'product.stocktaking',
                'cashier' => false,
                'description' => 'Comparison between physical stock count and system stock records.',
            ],
            [
                'name' => 'Product Performance Report',
                'template' => 'product.product-performance',
                'cashier' => false,
                'description' => 'Analysis of fast and slow moving products based on sales trends.',
            ],
            [
                'name' => 'Complete Transaction List',
                'template' => 'sales.transaction-list',
                'cashier' => true,
                'description' => 'A complete list of all transactions made in the system.',
            ],
            [
                'name' => 'Discount Usage Report',
                'template' => 'sales.discount-usage',
                'cashier' => true,
                'description' => 'Report showing how and when discounts are applied by cashiers or customers.',
            ],
            [
                'name' => 'Cash Flow Summary',
                'template' => 'finance.cash-flow-summary',
                'cashier' => false,
                'description' => 'Summary of all incoming and outgoing cash transactions.',
            ],
            [
                'name' => 'Income vs Expense Report',
                'template' => 'finance.income-vs-expense',
                'cashier' => false,
                'description' => 'Comparison report between total income and total expenses over a period.',
            ],
        ];

        return view('report.index', compact('reportTemplates', 'cashiers'));
    }

    public function generateReport(Request $request)
    {
        $startDate = now()->createFromFormat('Y-m-d', $request->start)->startOfDay();
        $endDate = now()->createFromFormat('Y-m-d', $request->end)->endOfDay();
        $data = [
            'customer_company' => session('userLogged')['company']['name'],
            'generated_date' => now(),
            'report_period' => Carbon::createFromFormat('Y-m-d', $request->start)->startOfDay().' -> '.Carbon::createFromFormat('Y-m-d', $request->end)->endOfDay(),
            'data' => $this->reportData($request->template, $request->cashier, $startDate, $endDate),
        ];
        $pdf = App::make('dompdf.wrapper');
        $pdf = $pdf->loadView('report.'.$request->template, ($data) ? $data : []);
        $pdf->render();
        $canvas = $pdf->getCanvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $text = session('userLogged')['company']['name'];
        $canvas->set_opacity(.05);
        $x = (($w - 250) / 2);
        $y = (($h - 250) / 2);
        $canvas->text($x, $y, $text, null, 50, [0, 0, 0], 0, 0, 45);
        $filename = Str::headline(str_replace('-', ' ', str_replace('.', '&', $request->template))).' '.session('userLogged')['company']['name'].'.pdf';
        $filename = preg_replace('/[\w]{1,}&/s', '', $filename);
        $pdf->add_info('Title', $filename);
        $pdf->add_info('Keywords', $filename);
        $pdf->add_info('Author', env('APP_NAME').' Report Service');

        return $pdf->stream($request->template.'_'.session('userLogged')['company']['name'].'.pdf');
    }

    private function reportData(string $template, $cashiers, $startDate, $endDate)
    {
        if ($template == 'sales.sales-summary') {
            return [
                'salesOverview' => $this->salesOverview($startDate, $endDate),
                'salesByCategory' => $this->salesByCategory($startDate, $endDate),
                'salesTopProduct' => $this->salesTopProduct($startDate, $endDate),
            ];
        } elseif ($template == 'sales.sales-by-product') {
            return [
                'salesProduct' => $this->salesProduct($startDate, $endDate),
            ];
        } elseif ($template == 'sales.sales-by-category') {
            return [
                'salesByCategory' => $this->salesByCategory($startDate, $endDate),
            ];
        } elseif ($template == 'sales.sales-by-cashier') {
            return [
                'salesByStaff' => $this->salesByStaff($cashiers, $startDate, $endDate),
            ];
        } elseif ($template == 'product.stock-on-hand') {
            return [
                'stockOnHand' => $this->stockOnHand($startDate, $endDate),
            ];
        } elseif ($template == 'product.stock-movements') {
            return [
                'stockMovement' => $this->stockMovement($startDate, $endDate),
            ];
        } elseif ($template == 'product.stocktaking') {
            return [
                'stockTaking' => $this->stockTaking($startDate, $endDate),
            ];
        } elseif ($template == 'product.product-performance') {
            return [
                'productPerformance' => $this->productPerformance($startDate, $endDate),
            ];
        } elseif ($template == 'sales.transaction-list') {
            return [
                'transactionComplete' => $this->transactionComplete($cashiers, $startDate, $endDate),
            ];
        } elseif ($template == 'sales.discount-usage') {
            return [
                'discountUsage' => $this->discountUsage($cashiers, $startDate, $endDate),
            ];
        } elseif ($template == 'finance.cash-flow-summary') {
            return [
                'cashFlow' => $this->cashFlow($startDate, $endDate),
            ];
        } elseif ($template == 'finance.income-vs-expense') {
            return [
                'incomeVsExpense' => $this->incomeVsExpense($startDate, $endDate),
            ];
        } else {
            return [];
        }
    }

    private function discountUsage($cashier, $startDate, $endDate)
    {
        return DB::table('transactions as cpt')->select(
            DB::raw('ROW_NUMBER() OVER (ORDER BY cpt.orderCode) as row_numbers'),
            DB::raw('DATE(cpt.created_at) as transaction_create'),
            'cpt.orderCode',
            'ccd.code',
            'ccd.description',
            'cpt.discount',
            'cpt.total',
            DB::raw('(cpt.total - cpt.discount) as total_after_discount'),
            'u.name'
        )
            ->join('discounts as ccd', 'cpt.discountId', '=', 'ccd.id')
            ->join('users as u', 'cpt.user_id', '=', 'u.id')
            ->where('cpt.user_id', $cashier)
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->groupBy('cpt.orderCode', 'cpt.created_at', 'ccd.code', 'ccd.description', 'cpt.discount', 'cpt.total', 'u.name')
            ->get();
    }

    private function productPerformance($startDate, $endDate)
    {
        $fastMoving = DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->leftJoin('products as cpg', 'cdpt.goodId', '=', 'cpg.id')
            ->join('product_categories as apt', 'cpg.unit_id', '=', 'apt.id')
            ->selectRaw('
        row_number() over ( ORDER BY sold) as row_numbers,
        cpg.name AS product_name,
        apt.name AS category,
        SUM(cdpt.quantity) AS sold,
        SUM(cpg.price * cdpt.quantity) AS sales,
        CAST(AVG(cdpt.quantity) OVER (PARTITION BY DATE(cpt.created_at)) as integer) AS sales_per_day
    ')
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->groupByRaw('cpg.name, DATE(cpt.created_at), apt.name, cpg.created_at')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        $slowMoving = DB::table('transaction_items as cdpt')
            ->rightJoin('products as cpg', 'cdpt.goodId', '=', 'cpg.id')
            ->join('product_categories as apt', 'cpg.unit_id', '=', 'apt.id')
            ->select(
                DB::raw('row_number() over ( ORDER BY cpg.name) as row_numbers'),
                'cpg.name as product_name',
                'apt.name as category',
                DB::raw('COALESCE(SUM(cdpt.quantity), 0) as sold'),
                DB::raw('COALESCE(SUM(cpg.price * cdpt.quantity), 0) as sales'),
                DB::raw('
            CASE
                WHEN cdpt.created_at IS NULL
                    THEN ABS(DAY(cpg.created_at) - DAY(CURRENT_DATE))
                ELSE ABS(DAY(CURRENT_DATE) - MAX(DAY(cdpt.created_at)))
            END as sales_per_day
        ')
            )->where('cpg.company_id', session('userLogged')['company']['id'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('cdpt.created_at', [$startDate, $endDate])
                    ->orWhereBetween('cpg.created_at', [$startDate, $endDate]);
            })
            ->groupByRaw('cpg.name, cdpt.created_at, apt.name, cpg.created_at')
            ->havingRaw('sales_per_day > 0')
            ->orderBy('sales_per_day', 'desc')
            ->limit(5)
            ->get();

        return ['fastMoving' => $fastMoving, 'slowMoving' => $slowMoving];
    }

    private function stockTaking($startDate, $endDate)
    {
        return DB::table('products as cpg')
            ->join('product_weight_units as agu', 'cpg.unit_id', '=', 'agu.id')
            ->leftJoin('customer_company_stocktakings as cps', 'cpg.id', '=', 'cps.goodId')
            ->select([
                'cpg.name',
                'agu.name as unit_name',
                DB::raw('CASE WHEN cps.expect_stock IS NOT NULL THEN cps.expect_stock ELSE cpg.stock END AS system_stock'),
                'cps.real_stock as physical_stock',
                DB::raw('(cps.expect_stock - cps.real_stock) AS difference'),
                'cpg.price as cost',
                DB::raw('(cpg.price * cps.expect_stock) - (cpg.price * cps.real_stock) AS value_diff'),
            ])
            ->where('cpg.company_id', session('userLogged')['company']['id'])
            ->where('cps.status', 1)
            ->whereBetween('cps.created_at', [$startDate, $endDate])
            ->orderBy('cps.created_at')
            ->get();
    }

    private function salesOverview($startDate, $endDate)
    {
        return DB::table('transactions as cpt')
            ->selectRaw('COALESCE(SUM(total), 0) AS total_sales,COUNT(id) AS total_orders,COALESCE(AVG(total), 0) AS avg_order_value')
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->where('cpt.orderCode', 'like', '%OUT%')
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->first();
    }

    private function salesByCategory($startDate, $endDate)
    {
        return DB::table('product_categories as apt')
            ->join('products as cpg', 'cpg.type_id', '=', 'apt.id')
            ->join('transaction_items as cdpt', 'cpg.id', '=', 'cdpt.goodId')
            ->join('transactions as cpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->select(
                'apt.name as category',
                DB::raw('SUM(cdpt.quantity) as total_quantity'),
                DB::raw('COUNT(cpt.orderCode) as total_orders'),
                'cpg.name as top_product'
            )
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->where('cpt.orderCode', 'like', '%OUT%')
            ->whereBetween('cdpt.created_at', [$startDate, $endDate])
            ->groupBy('apt.name', 'cpg.name')
            ->orderByDesc('cpg.name')
            ->limit(5)
            ->get();
    }

    private function salesTopProduct($startDate, $endDate)
    {
        return DB::table('transactions as cpt')
            ->leftJoin('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->leftJoin('products as cpg', 'cdpt.goodId', '=', 'cpg.id')
            ->select(
                'cpg.name',
                DB::raw('SUM(cdpt.quantity) as total_quantity'),
                DB::raw('SUM(cdpt.total) as total_amount'),
                DB::raw('AVG(cdpt.total) as average_amount')
            )
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->where('cpt.orderCode', 'like', '%OUT%')
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->groupBy('cpg.name', 'cpt.orderCode')
            ->limit(5)
            ->get();
    }

    private function salesProduct($startDate, $endDate)
    {
        return DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->join('products as cpg', 'cdpt.goodId', '=', 'cpg.id')
            ->join('product_categories as apt', 'apt.id', '=', 'cpg.type_id')
            ->select(
                'cpg.name as product_name',
                'apt.name as category',
                DB::raw('SUM(cdpt.quantity) as total_quantity'),
                'cpg.price',
                DB::raw('SUM(cdpt.total) as total_revenue')
            )
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->where('cpt.orderCode', 'like', '%OUT%')
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->groupBy('cpg.name', 'apt.name', 'cpg.price')
            ->get();
    }

    private function salesByStaff($user_id, $startDate, $endDate)
    {
        return DB::table('transactions as cpt')
            ->join('users as u', 'cpt.user_id', '=', 'u.id')
            ->select(
                'u.name as staff_name',
                DB::raw('COUNT(cpt.orderCode) as total_orders'),
                DB::raw('SUM(cpt.total) as total_sales'),
                DB::raw('AVG(cpt.total) as avg_order_value')
            )
            ->where('cpt.company_id', session('userLogged')['company']['id'])
            ->where('cpt.user_id', $user_id)
            ->where('cpt.orderCode', 'like', '%OUT%')
            ->whereBetween('cpt.created_at', [$startDate, $endDate])
            ->groupBy('u.name')
            ->get();
    }

    private function transactionComplete($user_id, $startDate, $endDate)
    {
        return DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->join('users as u', 'cpt.user_id', '=', 'u.id')
            ->join('products as ccg', 'ccg.id', '=', 'cdpt.goodId')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY cpt.orderCode) as row_numbers,
                DATE(cpt.created_at) as transaction_create,
                cpt.orderCode,
                COUNT(cdpt.id) as product_quantity,
                SUM(cdpt.quantity) as quantity,
                SUM(cdpt.quantity * ccg.price) as price,
                u.name
    ')
            ->where('cpt.user_id', 1)
            ->groupBy('cpt.orderCode', 'cpt.created_at', 'u.name')
            ->get();
    }

    private function stockOnHand($startDate, $endDate)
    {
        return DB::table('products as cpg')
            ->join('product_categories as apt', 'cpg.type_id', '=', 'apt.id')
            ->join('product_weight_units as agu', 'cpg.unit_id', '=', 'agu.id')
            ->select(
                'cpg.name',
                'apt.name as category',
                'agu.name as unit',
                'cpg.stock',
                'cpg.price',
                DB::raw('(cpg.stock * cpg.price) as total_value')
            )
            ->where('cpg.company_id', session('userLogged')['company']['id'])
            ->get();
    }

    private function stockMovement($startDate, $endDate)
    {
        $permanentQuery = DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->join('products as ccg', 'cdpt.goodId', '=', 'ccg.id')
            ->select([
                DB::raw('DATE(cdpt.created_at) as created_at'),
                'cdpt.orderCode',
                'cdpt.goodId',
                'ccg.name',
                'cdpt.quantity',
                'cdpt.stock_reference',
                DB::raw('(cdpt.stock_reference - cdpt.quantity) as balance'),
            ])->where('cpt.company_id', session('userLogged')['company']['id'])
            ->whereBetween('cdpt.created_at', [$startDate, $endDate]);

        $tempQuery = DB::table('adjustment_products as ctp')
            ->select([
                DB::raw('DATE(ctp.created_at) as created_at'),
                'ctp.orderCode',
                'ctp.customerCompanyGoodId',
                'ctp.name',
                DB::raw("
            CASE
                WHEN REPLACE(REGEXP_SUBSTR(orderCode, '-[A-Z]{2,7}-'), '-', '') = 'RESTOCK'
                     THEN (ctp.stock - ctp.stock_reference)
                ELSE ctp.stock
            END
        "),
                'ctp.stock_reference',
                DB::raw("
            CASE
                WHEN REPLACE(REGEXP_SUBSTR(orderCode, '-[A-Z]{2,7}-'), '-', '') = 'RESTOCK'
                     THEN ctp.stock
                ELSE (ctp.stock - ctp.stock_reference)
            END AS balance
        "),
            ])
            ->where('ctp.company_id', session('userLogged')['company']['id'])
            ->whereBetween('ctp.created_at', [$startDate, $endDate]);

        $stocktakingQuery = DB::table('customer_company_stocktakings as ccs')
            ->join('products as ccg', 'ccs.goodId', '=', 'ccg.id')
            ->select([
                DB::raw('DATE(ccs.created_at) as created_at'),
                DB::raw('null as orderCode'),
                'ccs.goodId',
                'ccg.name',
                DB::raw('abs(ccs.expect_stock - ccs.real_stock) as quantity'),
                'ccs.expect_stock as stock_reference',
                'ccs.real_stock as balance',
            ])->where('ccs.company_id', session('userLogged')['company']['id'])
            ->whereBetween('ccs.created_at', [$startDate, $endDate]);

        $unionQuery = $permanentQuery
            ->unionAll($tempQuery)
            ->unionAll($stocktakingQuery);

        $results = DB::table(DB::raw("({$unionQuery->toSql()}) as sub"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at')
            ->orderBy('orderCode')
            ->get();

        return $results;
    }

    private function cashFlow($startDate, $endDate)
    {
        $permanentQuery = DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->join('products as ccg', 'cdpt.goodId', '=', 'ccg.id')
            ->select([
                DB::raw('DATE(cdpt.created_at) as created_at'),
                'cdpt.orderCode',
                DB::raw('(ccg.price * cdpt.quantity) as amount'),
            ])->where('cpt.company_id', session('userLogged')['company']['id'])
            ->whereBetween('cdpt.created_at', [$startDate, $endDate]);

        $tempQuery = DB::table('adjustment_products as ctp')
            ->select([
                DB::raw('DATE(ctp.created_at) as created_at'),
                'ctp.orderCode',
                DB::raw('( ctp.price * ctp.stock) as amount'),
            ])
            ->where('ctp.company_id', session('userLogged')['company']['id'])
            ->whereBetween('ctp.created_at', [$startDate, $endDate]);
        $unionQuery = $permanentQuery
            ->unionAll($tempQuery);

        $results = DB::table(DB::raw("({$unionQuery->toSql()}) as sub"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at')
            ->orderBy('orderCode')
            ->get();

        return $results;
    }

    private function incomeVsExpense($startDate, $endDate)
    {
        $permanentQuery = DB::table('transactions as cpt')
            ->join('transaction_items as cdpt', 'cpt.orderCode', '=', 'cdpt.orderCode')
            ->join('products as ccg', 'cdpt.goodId', '=', 'ccg.id')
            ->select([
                DB::raw('DATE(cdpt.created_at) as created_at'),
                'cdpt.orderCode',
                DB::raw('(ccg.price * cdpt.quantity) as amount'),
            ])->where('cpt.company_id', session('userLogged')['company']['id'])
            ->whereBetween('cdpt.created_at', [$startDate, $endDate]);

        $tempQuery = DB::table('adjustment_products as ctp')
            ->select([
                DB::raw('DATE(ctp.created_at) as created_at'),
                'ctp.orderCode',
                DB::raw('( ctp.price * ctp.stock) as amount'),
            ])
            ->where('ctp.company_id', session('userLogged')['company']['id'])
            ->whereBetween('ctp.created_at', [$startDate, $endDate]);
        $unionQuery = $permanentQuery
            ->unionAll($tempQuery);

        $results = DB::table(DB::raw("({$unionQuery->toSql()}) as sub"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at')
            ->orderBy('orderCode')
            ->get();

        return $results;
    }
}
