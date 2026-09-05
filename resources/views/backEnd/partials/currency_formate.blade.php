@push('script')
<script>
    window.currencyFormat = @json(generalSetting()->currencyDetail);
</script>
<script>
    function currency_format_js(amount, format) {
        let decimals = format.decimal_digit ?? 0;
        let dec_sep = format.decimal_separator ?? "";
        let thou_sep = format.thousand_separator ?? "";
        let symCode = format.currency_type === 'C' ? format.code : format.symbol;

        let amountFormatted = Number(amount).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });

        // Replace separators
        amountFormatted = amountFormatted
            .replace(",", thou_sep)
            .replace(".", dec_sep);

        let space = format.space ? " " : "";

        if (format.currency_position === "S") {
            return symCode + space + amountFormatted;
        } else {
            return amountFormatted + space + symCode;
        }
    }
</script>
@endpush