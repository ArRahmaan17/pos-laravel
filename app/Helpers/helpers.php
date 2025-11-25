<?php

use App\Models\CustomerProductTransaction;
use App\Models\CustomerTemporaryProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('getRole')) {
    function getRole()
    {
        return session('userLogged')['role']['name'];
    }
}
function generateAffiliateCode()
{
    $array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $array_result = [];
    foreach (array_rand($array, 6) as $index => $value) {
        $array_result[] = $array[$value];
    }

    return implode('', $array_result);
}
function numberFormat($number)
{
    return number_format($number, 2, ',', '.');
}
function defaultPassword()
{
    return Str::lower(implode('', explode(' ', session('userLogged')['company']['name'])));
}
function buatSingkatan($kalimat)
{
    return strtoupper(implode('', array_map(fn($kata) => $kata[0] . $kata[1], explode(' ', $kalimat))));
}

if (! function_exists('lastCompanyOrderCode')) {
    function lastCompanyOrderCode($transaction_status = 'OUT', $date = null)
    {
        if (! $date) {
            $date = now()->format('Y-m-d');
        }
        if ($transaction_status == 'OUT') {
            $data = CustomerProductTransaction::where('orderCode', 'like', '%' . $transaction_status . '%')
                ->where('company_id', session('userLogged')['company']['id'])->whereRaw("DATE(created_at) = '" . $date . "'")
                ->orderBy('id', 'DESC')
                ->first();
        } else {
            $data = CustomerTemporaryProduct::where('orderCode', 'like', '%' . $transaction_status . '%')
                ->where('company_id', session('userLogged')['company']['id'])->where('transaction_created', $date)
                ->orderBy('id', 'DESC')
                ->first();
        }
        $lastOrder = buatSingkatan(session('userLogged')['company']['name']) . '-' . $transaction_status . '-' . $date . '-' . str_pad(1, 5, '0', STR_PAD_LEFT);
        if ($data && explode(
            buatSingkatan(session('userLogged')['company']['name']) . '-' . $transaction_status . '-' . $date . '-',
            $data->orderCode
        )) {
            $lastOrder = buatSingkatan(session('userLogged')['company']['name']) . '-' . $transaction_status . '-' . $date . '-' . str_pad(
                intval(
                    implode(
                        '',
                        explode(
                            buatSingkatan(session('userLogged')['company']['name']) . '-' . $transaction_status . '-' . $date . '-',
                            $data->orderCode
                        )
                    )
                ) + 1,
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        return $lastOrder;
    }
}

if (! function_exists('stringPad')) {
    function stringPad($word, $length = 2, $pad = '0', $type = STR_PAD_LEFT)
    {
        return str_pad($word, $length, $pad, $type);
    }
}

function unFormattedPhoneNumber($formattedNumber)
{
    $unformattedNumber = preg_replace('/\D/', '', $formattedNumber);
    if (substr($unformattedNumber, 0, 2) !== '62') {
        return 'Invalid Indonesian phone number.';
    }
    str_replace('62', '', $unformattedNumber);

    return $unformattedNumber;
}
function formatIndonesianPhoneNumber($phoneNumber)
{
    $cleaned = preg_replace('/\D/', '', $phoneNumber);
    if (strpos($cleaned, '62') === 0) {
        $cleaned = substr($cleaned, 2);
    }
    if ($cleaned[0] !== '+62') {
        $cleaned = '+62' . $cleaned;
    }
    $formatted = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})/', '$1 $2-$3-$4', $cleaned);

    return $formatted;
}

if (! function_exists('dataToOption')) {
    function dataToOption($allData, $attr = false)
    {
        $html = "<option value=''>Mohon Pilih</option>";
        foreach ($allData as $index => $data) {
            if ($attr) {
                $html .= "<option data-attr='" . $data->attribute . "' value='" . (isset($data->id) ? $data->id : $data->name) . "'>" . $data->name . ' ( Tersedia di ' . $data->attribute . ')</option>';
            } else {
                $html .= "<option value='" . (isset($data->id) ? $data->id : $data->name) . "'>" . $data->name . '</option>';
            }
        }

        return $html;
    }
}
function removeDuplicate(array $array, ?string $customKey = null)
{
    $uniqueIds = [];
    $uniqueArray = [];

    foreach ($array as $item) {
        // if custom key provided AND key exists in item → use it
        if ($customKey !== null && isset($item[$customKey])) {
            $id = $item[$customKey];
        } else {
            // fallback: use the whole item as the unique value (stringified)
            $id = is_array($item) ? json_encode($item) : $item;
        }

        if (!in_array($id, $uniqueIds, true)) {
            $uniqueIds[] = $id;
            $uniqueArray[] = $item;
        }
    }

    return $uniqueArray;
}


if (! function_exists('convertStringToNumber')) {
    function convertStringToNumber($string)
    {
        return implode('', explode('.', $string));
    }
}
if (! function_exists('convertAlphabeticalToNumberDate')) {

    function convertAlphabeticalToNumberDate($stringDate)
    {
        if ($stringDate != null) {
            $number = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
            $stringDate = explode(' ', $stringDate);
            switch ($stringDate[1]) {
                case 'Januari':
                    $str = $stringDate[2] . '-' . $number[0] . '-' . $stringDate[0];
                    break;
                case 'Februari':
                    $str = $stringDate[2] . '-' . $number[1] . '-' . $stringDate[0];
                    break;
                case 'Maret':
                    $str = $stringDate[2] . '-' . $number[2] . '-' . $stringDate[0];
                    break;
                case 'April':
                    $str = $stringDate[2] . '-' . $number[3] . '-' . $stringDate[0];
                    break;
                case 'Mei':
                    $str = $stringDate[2] . '-' . $number[4] . '-' . $stringDate[0];
                    break;
                case 'Juni':
                    $str = $stringDate[2] . '-' . $number[5] . '-' . $stringDate[0];
                    break;
                case 'Juli':
                    $str = $stringDate[2] . '-' . $number[6] . '-' . $stringDate[0];
                    break;
                case 'Agustus':
                    $str = $stringDate[2] . '-' . $number[7] . '-' . $stringDate[0];
                    break;
                case 'September':
                    $str = $stringDate[2] . '-' . $number[8] . '-' . $stringDate[0];
                    break;
                case 'Oktober':
                    $str = $stringDate[2] . '-' . $number[9] . '-' . $stringDate[0];
                    break;
                case 'November':
                    $str = $stringDate[2] . '-' . $number[10] . '-' . $stringDate[0];
                    break;
                case 'Desember':
                    $str = $stringDate[2] . '-' . $number[11] . '-' . $stringDate[0];
                    break;
                default:
                    $str = $stringDate[2] . '- not valid -' . $stringDate[0];
                    break;
            }

            return $str;
        } else {
            return $stringDate;
        }
    }
}
if (! function_exists('convertNumericDateToAlphabetical')) {
    function convertNumericDateToAlphabetical($stringDate)
    {
        if ($stringDate != null) {
            $number = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $stringDate = explode('-', $stringDate);
            switch ($stringDate[1]) {
                case '01':
                    $str = $stringDate[2] . ' ' . $number[0] . ' ' . $stringDate[0];
                    break;
                case '02':
                    $str = $stringDate[2] . ' ' . $number[1] . ' ' . $stringDate[0];
                    break;
                case '03':
                    $str = $stringDate[2] . ' ' . $number[2] . ' ' . $stringDate[0];
                    break;
                case '04':
                    $str = $stringDate[2] . ' ' . $number[3] . ' ' . $stringDate[0];
                    break;
                case '05':
                    $str = $stringDate[2] . ' ' . $number[4] . ' ' . $stringDate[0];
                    break;
                case '06':
                    $str = $stringDate[2] . ' ' . $number[5] . ' ' . $stringDate[0];
                    break;
                case '07':
                    $str = $stringDate[2] . ' ' . $number[6] . ' ' . $stringDate[0];
                    break;
                case '08':
                    $str = $stringDate[2] . ' ' . $number[7] . ' ' . $stringDate[0];
                    break;
                case '09':
                    $str = $stringDate[2] . ' ' . $number[8] . ' ' . $stringDate[0];
                    break;
                case '10':
                    $str = $stringDate[2] . ' ' . $number[9] . ' ' . $stringDate[0];
                    break;
                case '11':
                    $str = $stringDate[2] . ' ' . $number[10] . ' ' . $stringDate[0];
                    break;
                case '12':
                    $str = $stringDate[2] . ' ' . $number[11] . ' ' . $stringDate[0];
                    break;
                default:
                    $str = $stringDate[2] . '  not valid  ' . $stringDate[0];
                    break;
            }

            return $str;
        } else {
            return $stringDate;
        }
    }
}

if (! function_exists('buildTree')) {
    function buildTree(array &$elements, $idParent = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($idParent === $element['parent']) {
                $children = buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
if (! function_exists('buildTreeMenu')) {
    function buildTreeMenu(array &$elements, $idParent = '0', $key = 'id')
    {
        $branch = [];
        foreach ($elements as $element) {
            $element = (array) $element;
            if ($element['parent'] === $idParent) {
                $children = buildTreeMenu($elements, $element[$key]);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}

// if (!function_exists('arrayTree')) {
//     function arrayTree($parentArrays, $array)
//     {
//         $result = [];
//         foreach ($parentArrays as $index => $parent) {
//             foreach ($array as $indexArray => $value) {
//                 if ($value['parent'] == $parent) {
//                     $result[$parentArrays[$index]][] = $value;
//                 }
//             }
//         }
//         return $result;
//     }
// }

if (!function_exists('arrayTree')) {
    function arrayTree(&$elements, $key = 'parent', $idParent = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            $element = (array) $element;
            if ($element['parent'] === $idParent) {
                $children = buildTreeMenu($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}

function getSql($model)
{
    $replace = function ($sql, $bindings) {
        $needle = '?';
        foreach ($bindings as $replace) {
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === 'string') {
                    $replace = ' "' . addslashes($replace) . '" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }

        return $sql;
    };
    $sql = $replace($model->toSql(), $model->getBindings());

    return $sql;
}
// function formatIndonesianPhoneNumber($){
//     // Check if the number starts with the country code and remove it
//     if (strpos($cleaned, '62') === 0) {
//         $cleaned = substr($cleaned, 2);
//     }

//     // Ensure the number starts with 0
//     if ($cleaned[0] !== '+62') {
//         $cleaned = '+62' . $cleaned;
//     }

//     // Format the number (e.g., (021) 123-4567 or 0812-345-6789)
//     // This is just a basic example; you may need to adjust formatting based on specific needs
//     $formatted = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})/', '$1 $2-$3-$4', $cleaned);

//     return $formatted;
// }
function statusTransaction($orderCode)
{
    preg_match('/-[A-Z]{2,7}-/i', $orderCode, $result);

    return str_replace('-', '', $result[0]);
}
if (! function_exists('checkPermissionMenu')) {
    function checkPermissionMenu($id, $role)
    {
        return DB::table('customer_role_accessibilities')->where(['menuId' => $id, 'role_id' => $role])->count() > 0 ? true : false;
    }
}
if (! function_exists('buildMenu')) {

    function buildMenu(array &$elements, $place = 0)
    {
        $html = '';
        foreach ($elements as $element) {
            if (getRole() == 'Developer' || (getRole() == 'Manager' && $element['dev_only'] == 0) || checkPermissionMenu($element['id'], session('userLogged')['role_id'])) {
                if ($place == 0) {
                    if (isset($element['children'])) {
                        $children = buildMenu($element['children']);
                        $html .= '<li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ' . $element['icon'] . '"></i>
                            <div data-i18n="Layouts">' . $element['name'] . '</div>
                        </a>

                        <ul class="menu-sub">' . $children . '</ul>
                    </li>';
                    } else {
                        $html .= '<li class="menu-item">
                    <a href="' . (Route::has($element['route']) ? route($element['route']) : $element['route']) . '" class="menu-link ' . (Route::is($element['route']) ? 'bg-primary text-white rounded-sm' : '') . '">
                        <i class="menu-icon tf-icons ' . $element['icon'] . '"></i>
                        <div data-i18n="' . $element['name'] . '">' . $element['name'] . '</div>
                    </a>
                </li>';
                    }
                } elseif ($place == 1) {
                    $html .= '<li>
                        <a class="dropdown-item ' . (Route::is($element['route']) ? 'bg-primary' : '') . '" href="' . (Route::has($element['route']) ? route($element['route']) : $element['route']) . '">
                            <span class="d-flex align-items-center align-middle ' . (Route::is($element['route']) ? 'bg-primary rounded-sm text-white' : '') . '">
                                <i class="flex-shrink-0 me-2 ' . $element['icon'] . '"></i>
                                <span class="flex-grow-1 align-middle">' . $element['name'] . '</span>
                            </span>
                        </a>
                    </li>';
                }
            }
        }

        return $html;
    }
}

if (! function_exists('buildMenuRoleAccessibillity')) {
    function buildMenuRoleAccessibillity(array &$elements)
    {
        $html = '';
        foreach ($elements as $element) {
            $html .= '<div class="form-check">
                            <input class="form-check-input" type="checkbox" value="' . $element['id'] . '"
                                id="option-' . ($element['parent'] ?? '0') . '-' . $element['id'] . '">
                            <label for="option-' . ($element['parent'] ?? '0') . '-' . $element['id'] . '" class="form-check-label">
                                ' . str_replace('-', ' ', $element['id']) . '
                            </label>
                        </div>';
            if (isset($element['children'])) {
                $html .= '<div class="container-fluid">' . buildMenuRoleAccessibillity($element['children']) . "</div>";
            }
        }

        return $html;
    }
}
if (! function_exists('limitOffsetToArray')) {

    function limitOffsetToArray($limit = 5, $offset = 1)
    {
        $data = [];
        for ($i = ($limit + $offset) - $limit; $i < ($limit + $offset); $i++) {
            array_push($data, $i);
        }

        return $data;
    }
}
