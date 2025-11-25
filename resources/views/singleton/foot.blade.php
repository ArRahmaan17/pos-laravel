<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/iziModal.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
<script src="{{ asset('assets/js/pages-pricing.js') }}"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
@if (env('APP_ENV') === 'production')
    <script>
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        })
    </script>
@endif
<script>
    window.process_subscription = null;
    window.serverTime = undefined;
    window.intervalTime = undefined;
    window.company = `{{ buatSingkatan(session('userLogged')['company']['name']) }}`;

    function lockscreenTrigger() {
        $('.container-p-y').addClass('blur')
        setTimeout(() => {
            $('.lockscreen').offcanvas('show');
        }, 1000);
        $.ajax({
            type: "POST",
            url: "{{ route('auth.lockscreen') }}",
            data: {
                _token: `{{ csrf_token() }}`,
            },
            dataType: "json",
            success: function(response) {}
        });
        $('#form-lockscreen').submit(function(e) {
            e.preventDefault();
            let data = serializeObject($('#form-lockscreen'));
            $.ajax({
                type: "POST",
                url: "{{ route('auth.unlock-screen') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    $('.lockscreen').offcanvas('hide');
                    $('.blur').removeClass('blur')
                }
            });
        });
    }

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    function dataToOption(allData, attr = false) {
        let html = "<option value=''>Mohon Pilih</option>";

        allData.forEach(data => {
            if (attr) {
                html +=
                    `<option data-attr="${data.attribute}" value="${data.id ? data.id : data.name}">${data.name} ( ${data.attribute} )</option>`;
            } else {
                html += `<option value="${data.id ? data.id : data.name}">${data.name}</option>`;
            }
        });

        return html;
    }

    function serializeFiles(node) {
        let form = $(node),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            if ($(tag)[0].files.length > 0) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            }
        });

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };

    function copyToClipboard(element = 'registration-link-code', ) {
        const codeSnippet = (document.getElementById(element).innerText != '') ? document.getElementById(element).innerText : document.getElementById(
            element).value;
        const tempTextArea = document.createElement('textarea');
        tempTextArea.value = codeSnippet;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        navigator.clipboard.writeText(tempTextArea.value);
        iziToast.success({
            id: 'alert-create-registration-link-action',
            title: 'Success',
            message: `Coppied text ${codeSnippet}`,
            position: 'topRight',
            layout: 1,
            displayMode: 'replace'
        });
        tempTextArea.remove();
    }

    function serializeObject(node) {
        var o = {};
        var a = node.serializeArray();
        $.each(a, function() {
            if (this.value !== "") {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            }
        });
        return o;
    }

    function removeDuplicates(array) {
        return array.filter((value, index) => array.indexOf(value) === index);
    }

    function numberFormat(nilai, prefix = 'Rp. ') {
        return $.fn.dataTable.render.number('.', ',', 2, prefix).display(nilai);
    }

    function buildTree(elements, parentId = 0) {
        var branch = [];
        elements.forEach(element => {
            if (element['parent'] == parentId) {
                var children = buildTree(elements, element['id']);
                if (children.length > 0) {
                    element['children'] = children;
                }
                branch.push(element);
            }
        });
        return branch
    }

    function kebabCase(string) {
        return string.trim().toLowerCase().split(' ').join('-')
    }

    function server_time(date = `{{ $serverTime }}`) {
        if (window.intervalTime) {
            clearInterval(window.intervalTime)
        }
        window.serverTime = moment(date).format('YYYY-MM-DD HH:mm:ss')
        window.intervalTime = setInterval(() => {
            window.serverTime = moment(window.serverTime).add('1', 's').format('YYYY-MM-DD HH:mm:ss');
            $('.serverTime').html(window.serverTime)
        }, 1000)
    }

    function formattedInput() {
        $('.phone_number').inputmask('(+62) 999-999-9999[9]')
        $('.price').inputmask('currency', {
            radixPoint: ',',
            groupSeparator: ".",
            rightAlign: false,
            allowMinus: false
        });
        $('.number').inputmask('integer', {
            groupSeparator: ".",
            rightAlign: false,
            allowMinus: false
        });

        $('.single_number').inputmask({
            mask: "9{1}",
            placeholder: "",
        });
        $('.email').inputmask({
            mask: "*{1,15}[.*{1,15}][.*{1,15}][.*{1,15}]@*{1,15}[.*{2,6}][.*{1,2}]",
            greedy: false,
            definitions: {
                '*': {
                    casing: "lower",
                    validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                },
            },
            onBeforePaste: function(pastedValue, opts) {
                pastedValue = pastedValue.toLowerCase();
                return pastedValue.replace("mailto:", "");
            },
        });
    }
    $(function() {
        server_time();
        $(".menu-sub").find('.menu-link.bg-primary').parents('.menu-item:not(:first)').map((index, element) => {
            $(element).addClass('open');
            $(element).children('.menu-link.menu-toggle').addClass('bg-primary text-white')
        });
        $.extend($.fn.dataTable.defaults, {
            "pageLength": 5,
            "aLengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            "responsive": true
        });
        $.ajaxSetup({
            complete: function(e, status) {
                server_time(e.getResponseHeader('Date'))
            }
        });
        $('.process-subscription').click(function() {
            window.process_subscription = {
                id: $(this).data('subscription'),
                year: $('.price-duration-toggler').prop('checked')
            };
            $('#AppSubscriptionModal').modal('hide');
            $('#SubscriptionProcessModal').modal('show');
        });
        $('#SubscriptionProcessModal').on('shown.bs.modal', function() {
            if (window.process_subscription == null) {
                $(this).modal('hide')
            } else {
                $.ajax({
                    type: "get",
                    url: `{{ route('dev.subscription.show') }}/${window.process_subscription.id}`,
                    dataType: "json",
                    success: function(response) {
                        $('.subs-title').html(response.data.name);
                        $('.subs-description').html(response.data.description);
                        $('.subs-price').html(numberFormat(response.data.price - (window.process_subscription.year ? response
                            .data.price * 5 / 100 : 0)));
                        $('.subs-sub-total').html(numberFormat((response.data.price - (window.process_subscription.year ? (
                            response.data.price * 5 / 100) : 0)) * (window.process_subscription.year ? 12 : 1)));
                    }
                });
            }
        });
        $('#SubscriptionProcessModal').on('hidden.bs.modal', function() {
            window.process_subscription == null;
        });
    });
    $("#modalDisconect").iziModal({
        title: 'Warning',
        subtitle: 'You About To Disconected',
        headerColor: '#ff3e1d',
        radius: 3,
        zindex: 9999,
        width: 900,
        navigateCaption: true,
        restoreDefaultContent: false,
        timeout: 120000,
        timeoutProgressbar: true,
        onClosed: function() {
            $('.lockscreen').offcanvas('show');
        }
    });
    $('.offcanvas input').keydown(function(e) {
        if (e.which == 9) {
            e.preventDefault();
        }
    });
    $('.single_number').keyup(function(e) {
        if (e.currentTarget.value.split('').length == 1 && /\d{1}/y.exec(e.currentTarget.value) != null) {
            if (e.currentTarget.nextElementSibling) {
                $(e.currentTarget.nextElementSibling).focus();
            } else {
                $($(e.currentTarget).parents('.mb-3')[0].nextElementSibling).find('.single_number:first').focus()
            }
        }
    });
    @if (session('lifetime') !== null)
        @if (in_array(now()->createFromTimeString($serverTime, 'Asia/Jakarta')->diffInMinutes(now()->createFromTimeString(session('lifetime'), 'Asia/Jakarta'), false),
                [2, 1]))
            $("#modalDisconect").iziModal('open');
        @elseif (in_array(now()->createFromTimeString($serverTime, 'Asia/Jakarta')->diffInMinutes(now()->createFromTimeString(session('lifetime'), 'Asia/Jakarta'), false),
                [5, 4, 3]))
            iziToast.warning({
                id: 'alert-session-expirated',
                title: 'Alert',
                message: `session expirate in {{ now()->createFromTimeString($serverTime, 'Asia/Jakarta')->diffInMinutes(now()->createFromTimeString(session('lifetime'), 'Asia/Jakarta')) }} minutes`,
                position: 'bottomRight',
                layout: 2,
                balloon: true,
                displayMode: 'replace'
            });
        @elseif (now()->createFromTimeString($serverTime, 'Asia/Jakarta')->diffInMinutes(now()->createFromTimeString(session('lifetime'), 'Asia/Jakarta'), false) < 0 || session('lifetime') == null)
            lockscreenTrigger();
        @endif
    @else
        lockscreenTrigger();
    @endif
    $('.trigger-lockscreen').click(function() {
        lockscreenTrigger();
    });
</script>
