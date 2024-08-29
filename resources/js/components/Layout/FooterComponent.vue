<template>
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline"></div>
        <strong>{{ $t('footer.copyright_text') }} &copy; 2024 <a class="text-decoration-none"
                href="javascript:void(0)">{{ $t('footer.site_name') }}</a>.</strong> {{ $t('footer.right_reserved_text')
        }}
    </footer>
</template>
<script>

export default {
    name: 'FooterComponent',
};
</script>
