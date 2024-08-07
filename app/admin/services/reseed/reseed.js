// 字段 辅种站点名字 site
update_render_callable.push(
    function () {
        // 辅种站点：全选｜取消全选｜反选
        layui.util.on('lay-on', {
            // 全选|取消全选
            select_all: function () {
                let $ = layui.$;
                let elem = $('#select_all');
                let isCheckedAll = elem.attr('checked');
                if (isCheckedAll) {
                    elem.removeAttr('checked').text('全选');
                } else {
                    elem.attr('checked', true).text('取消全选');
                }
                $('#sites input[type="checkbox"]').prop('checked', !isCheckedAll);
                form.render();
            },
            // 反选
            select_invert: function () {
                let $ = layui.$;
                $('#sites input[type="checkbox"]').each(function () {
                    $(this).prop('checked', !$(this).prop('checked'));
                });
                form.render();
            }
        });

        layui.use(["jquery", "xmSelect", "popup", "laytpl"], function () {
            let laytpl = layui.laytpl;
            // 辅种站点
            layui.$.ajax({
                url: "/admin/site/select?format=select&limit=1000&field=site&disabled=0&simple=1",
                dataType: "json",
                success: function (res) {
                    let getTpl = document.getElementById('sites_tpl').innerHTML; // 获取模板字符
                    let elemView = document.getElementById('sites'); // 视图对象
                    // 渲染并输出结果
                    laytpl(getTpl).render(res.data, function (str) {
                        elemView.innerHTML = str;
                        let value = layui.$("#parameter").attr("value");
                        if (value) {
                            let parameter = JSON.parse(value);
                            if (parameter['sites']) {
                                layui.each(parameter['sites'], function (key, value) {
                                    init_element_attr_value('*[name="parameter[sites][' + key + ']"]', value);
                                });
                            }
                        }
                        form.render();
                    });
                    if (res.code) {
                        layui.popup.failure(res.msg);
                    }
                }
            });

            // 辅种下载器
            layui.$.ajax({
                url: "/admin/client/select?format=select&enabled=1&limit=1000",
                dataType: "json",
                success: function (res) {
                    const original = JSON.stringify(res.data);
                    const result = res.data;
                    let getTpl = document.getElementById('clients_tpl').innerHTML; // 获取模板字符
                    let elemView = document.getElementById('clients'); // 视图对象
                    // 渲染并输出结果
                    laytpl(getTpl).render(result, function (str) {
                        elemView.innerHTML = str;
                        let value = layui.$("#parameter").attr("value");
                        if (value) {
                            let parameter = JSON.parse(value);
                            if (parameter['clients']) {
                                layui.each(parameter['clients'], function (key, value) {
                                    init_element_attr_value('*[name="parameter[clients][' + key + ']"]', value);
                                });
                            }

                            // 渲染通知渠道
                            if (parameter['notify_channel']) {
                                $('input[name="parameter[notify_channel]').each(function () {
                                    $(this).prop('checked', $(this).val() === parameter['notify_channel']);
                                });
                            }

                            // 渲染标记规则
                            if (parameter['marker']) {
                                $('input[name="parameter[marker]').each(function () {
                                    $(this).prop('checked', $(this).val() === parameter['marker']);
                                });
                            }

                            // 自动校验
                            $('#auto_check').attr("checked", Boolean(parameter['auto_check']));
                        }
                        form.render();
                    });

                    // 主辅分离，统一辅种到此下载器
                    laytpl(document.getElementById('master_tpl').innerHTML).render(result, function (str) {
                        document.getElementById('master').innerHTML = str;
                        let value = layui.$("#parameter").attr("value");
                        if (value) {
                            let parameter = JSON.parse(value);
                            if (parameter['master']) {
                                $('input[name="parameter[master]').each(function () {
                                    $(this).prop('checked', $(this).val() === parameter['master']);
                                });
                            }
                        }
                        form.render();
                    });

                    if (res.code) {
                        layui.popup.failure(res.msg);
                    }
                }
            });

            // 路径过滤器
            layui.$.ajax({
                url: "/admin/folder/select?format=select&limit=1000",
                dataType: "json",
                success: function (res) {
                    let value = layui.$("#parameter").attr("value");
                    let parameter = value ? JSON.parse(value) : {};
                    let path_filter = parameter['path_filter'] || null;
                    let initPathFilterValue = path_filter ? path_filter.split(",") : [];
                    layui.xmSelect.render({
                        el: "#path_filter",
                        name: "parameter[path_filter]",
                        initValue: initPathFilterValue,
                        filterable: true,
                        tips: '请选择排除目录',
                        data: res.data,
                        //model: {"icon": "hidden", "label": {"type": "text"}},
                    });

                    if (res.code) {
                        layui.popup.failure(res.msg);
                    }
                }
            });
        });
    }
);
