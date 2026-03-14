<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/iziModal.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
<script src="{{ asset('assets/js/pages-pricing.js') }}"></script>
@stack('resource-js')
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
    window.company = `{{ session('userLogged') ? buatSingkatan(session('userLogged')['company']['name']) : '' }}`;

    function lockscreenTrigger() {
        $('.container-p-y').addClass('blur')
        setTimeout(() => {
            $('.lockscreen').offcanvas('show');
        }, 500);
        $.ajax({
            type: "POST",
            url: "{{ route('auth.lockscreen') }}",
            success: function(response) {}
        });
        $('title').html(`Lockscreen - {{ env('APP_NAME') }}`);
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
                    $('.blur').removeClass('blur');
                    $('title').html(`${$('meta[name="title_page"]').attr('content')} - {{ env('APP_NAME') }}`);
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
            if (element['parent'] === parentId) {
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
        window.serverTime = moment(date, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
        window.intervalTime = setInterval(() => {
            window.serverTime = moment(window.serverTime, 'YYYY-MM-DD HH:mm:ss').add('1', 's').format('YYYY-MM-DD HH:mm:ss');
            $('.serverTime').html(window.serverTime)
        }, 1000)
    }

    function formattedInput() {
        $('.phone_number').inputmask('+628-999-999-999[9]')
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
        formattedInput();
        server_time();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                },
            });
        } else {
            console.error("Kesalahan: CSRF Token tidak ditemukan di meta tag.");
        }
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
        $('.process-subscription').click(function() {
            window.process_subscription = {
                id: $(this).data('subscription'),
                year: $('.price-duration-toggler').prop('checked')
            };
            $('#AppSubscriptionModal').modal('hide');
            $('#SubscriptionProcessModal').modal('show');
        });
        $('#SubscriptionProcessModal').on('shown.bs.modal', function() {
            if (window.process_subscription === null) {
                $(this).modal('hide')
            } else {
                $.ajax({
                    type: "get",
                    url: `{{ route('settings.subscription.show') }}/${window.process_subscription.id}`,
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
            window.process_subscription === null;
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
            if (e.which === 9) {
                e.preventDefault();
            }
        });
        $('.single_number').keyup(function(e) {
            if (e.currentTarget.value.split('').length === 1 && /\d{1}/y.exec(e.currentTarget.value) != null) {
                if (e.currentTarget.nextElementSibling) {
                    $(e.currentTarget.nextElementSibling).focus();
                } else {
                    $($(e.currentTarget).parents('.mb-3')[0].nextElementSibling).find('.single_number:first').focus()
                }
            }
        });
    });
</script>
@if (session('lifetime') !== null)
    <script>
        const session_lifetime = `{{ session('lifetime') }}`;
    </script>
@else
    <script>
        $(function() {
            lockscreenTrigger();
        });
    </script>
@endif
@stack('js')
