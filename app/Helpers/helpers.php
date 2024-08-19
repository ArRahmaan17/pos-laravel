<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getRoleId')) {
    function getRoleId()
    {
        return session('user')->idrole;
    }
}
if (!function_exists('getRole')) {
    function getRole()
    {
        return DB::table('auth.role')->where('idrole', session('user')->idrole)->first()->role;
    }
}
if (!function_exists('splitKodeGolongan')) {
    function splitKodeGolongan($kodegolongan)
    {
        return implode('.', str_split($kodegolongan));
    }
}
if (!function_exists('stringPad')) {
    function stringPad($word, $length = 2, $pad = '0', $type = STR_PAD_LEFT)
    {
        return str_pad($word, $length, $pad, $type);
    }
}
if (!function_exists('dataToOption')) {
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
function removeDuplicate($array)
{
    // Initialize an empty array to store unique IDs
    $uniqueIds = [];

    // Iterate through the input array
    foreach ($array as $item) {
        // Check if the ID of the current item exists in $uniqueIds array
        if (!in_array($item, $uniqueIds)) {
            // If ID doesn't exist, add it to $uniqueIds and keep the item
            $uniqueIds[] = $item;
            $uniqueArray[] = $item;
        }
        // If ID already exists, skip adding it to $uniqueArray (thus removing duplicate)
    }

    // Return the array with unique IDs
    return $uniqueArray;
}

if (!function_exists('classificationType')) {
    function classificationType(array $conditions)
    {
        return DB::table('masterkapitalisasi')->where('kodegolongan', $conditions['kodegolongan'])->where('kodebidang', $conditions['kodebidang'])->first();
    }
}
if (!function_exists('getKoderegister')) {

    function getKoderegister(array $paramRegister)
    {
        $allowedColumn = [
            'kodeurusan',
            'kodesuburusan',
            'kodeorganisasi',
            'kodesuborganisasi',
            'kodeunit',
            'kodesubunit',
            'kodesubsubunit',
            'kodegolongan',
            'kodebidang',
            'kodekelompok',
            'kodesub',
            'kodesubsub',
            'tahunorganisasi',
            'tahunperolehan',
        ];

        $tmpWhere = [];

        $query = DB::table('kib')
            ->where('tahunorganisasi', 2024)
            ->where('statusdata', 'aktif');

        $akumLength = 0;
        foreach ($paramRegister as $key => $value) {
            if (in_array($key, $allowedColumn)) {
                $query->where($key, $value);
                $akumLength++;
                array_push($tmpWhere, $key);
            }
        }
        if ($akumLength < count($allowedColumn)) {
            throw new \Exception("Kolom yang diminta kurang! berikut daftarnya : \n" . implode("\n", array_diff($allowedColumn, $tmpWhere)), 1);
        }

        return ($query->max('koderegister') ?? 0) + 1;
    }
}
if (!function_exists('convertStringToNumber')) {
    function convertStringToNumber($string)
    {
        return implode('', explode('.', $string));
    }
}
if (!function_exists('getkdunit')) {
    function getkdunit($sp2d)
    {
        return DB::table('anggaran.sp2d')
            ->where([
                'kdper' => $sp2d['kdper'],
                'nosp2d' => $sp2d['nosp2d'],
                'tglsp2d' => $sp2d['tglsp2d'],
            ])->first()->kdunit;
    }
}
if (!function_exists('convertAlphabeticalToNumberDate')) {

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
if (!function_exists('convertNumericDateToAlphabetical')) {
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
if (!function_exists('kodeOrganisasi')) {
    function kodeOrganisasi()
    {
        $copi = clone session('organisasi');
        unset($copi->organisasi, $copi->tahunorganisasi, $copi->wajibsusut);

        return implode('.', array_values((array) $copi));

        return implode('.', array_values((array) $copi));

        return implode('.', array_values((array) $copi));
    }
}
if (!function_exists('getOrganisasi')) {
    function getOrganisasi()
    {
        return session('organisasi')->organisasi;
    }
}
if (!function_exists('buildTree')) {
    function buildTree(array &$elements, $idParent = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            $element = (array) $element;
            if ($element['parent'] == $idParent) {
                $children = buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                unset($element['parent']);
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
if (!function_exists('buildTreeMenu')) {

    function buildTreeMenu(array &$elements, $idParent = '0')
    {
        $branch = [];
        foreach ($elements as $element) {
            $element = (array) $element;
            if ($element['parent'] === $idParent) {
                $children = buildTreeMenu($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                unset($element['parent']);
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
if (!function_exists('buildTreeOrganisasi')) {
    function buildTreeOrganisasi(array &$elements, $idParent = '0')
    {
        $branch = [];
        foreach ($elements as $element) {
            $element = (array) $element;
            if ($element['parent'] === $idParent) {
                // dd($elements, $element['parent'], $element['id']);
                $children = buildTreeOrganisasi($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                unset($element['parent']);
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
if (!function_exists('checkPermissionMenu')) {
    function checkPermissionMenu($id, $role)
    {
        return DB::table('auth.role_menu')->where(['idmenu' => $id, 'idrole' => $role])->count() > 0 ? true : false;
    }
}
if (!function_exists('buildMenu')) {

    function buildMenu(array &$elements, $app = 'aset', $place = 'sidebar')
    {
        $html = '';
        foreach ($elements as $element) {
            $element = (array) $element;
            if (checkPermissionMenu($element['id'], session('user')->idrole, $app)) {
                if ($place == 'sidebar' && $app == $element['app']) {
                    if (isset($element['children'])) {
                        $children = buildMenu($element['children'], $app);
                        $html .= '<li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ' . $element['icons'] . '"></i>
                            <div data-i18n="Layouts">' . $element['nama'] . '</div>
                        </a>

                        <ul class="menu-sub">' . $children . '</ul>
                    </li>';
                    } else {
                        $html .= '<li class="menu-item ' . (app('request')->route()->named($element['link']) ? 'active' : '') . '">
                    <a href="' . route($element['link']) . '" class="menu-link ">
                        <i class="menu-icon tf-icons ' . $element['icons'] . '"></i>
                        <div data-i18n="Analytics">' . $element['nama'] . '</div>
                    </a>
                </li>';
                    }
                } elseif ($place == 'profile' && $app == $element['app']) {
                    $html .= '<li>
                        <a class="dropdown-item ' . (app('request')->route()->named($element['link']) ? 'active' : '') . '" href="' . route($element['link']) . '">
                            <span class="d-flex align-items-center align-middle">
                                <i class="flex-shrink-0 me-2 ' . $element['icons'] . '"></i>
                                <span class="flex-grow-1 align-middle">' . $element['nama'] . '</span>
                            </span>
                        </a>
                    </li>';
                }
            }
        }

        return $html;
    }
}
if (!function_exists('limitOffsetToArray')) {

    function limitOffsetToArray($limit = 5, $offset = 1)
    {
        $data = [];
        for ($i = ($limit + $offset) - $limit; $i < ($limit + $offset); $i++) {
            array_push($data, $i);
        }

        return $data;
    }
}


if (!function_exists('generateIdKontrak')) {

    function generateNextIdKontrak()
    {
        $data = DB::table('kontrak')
            ->orderByDesc('id')
            ->first();
        // dd($data);
        $next = $data ? $data->id + 1 : 1;
        // dd($next);

        return $next;
    }
}
