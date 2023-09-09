{#
# Copyright (c) 2023 Deciso B.V.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without modification,
# are permitted provided that the following conditions are met:
#
# 1. Redistributions of source code must retain the above copyright notice,
#    this list of conditions and the following disclaimer.
#
# 2. Redistributions in binary form must reproduce the above copyright notice,
#    this list of conditions and the following disclaimer in the documentation
#    and/or other materials provided with the distribution.
#
# THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
# INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
# AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
# AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
# OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.
#}

<script>
    'use strict';
    function get_backups(){
        console.log($("#providers").val());
    }
    $( document ).ready(function () {
        ajaxGet('/api/core/backup/providers', {}, function(data, status){
            if (data.items && Object.keys(data.items).length > 1) {
                let target = $("#providers").empty();
                Object.keys(data.items).forEach(function(key) {
                    target.append($("<option/>").attr('value', key).text(data.items[key].description));
                });
                target.selectpicker('refresh');
            }
            $("#providers").change(get_backups);
            $("#providers").change();
        });
    });

</script>


<div class="tab-content content-box">
    <div class="row">
        <div class="col-xs-12" style="height: 100px;">
            <select id="providers" class="selectpicker">
                <option value="this" selected>{{ lang._('This Firewall')}}</option>
            </select>
            <select class="selectpicker">
                <option data-content="First Line<br>Second Line">Mustard</option>
                <option data-content="House Number Street<br>City, State<br>Zip Code">Ketchup</option>
                <option data-content="Single line address">Third Address</option>
              </select>
        </div>
    </div>
</div>
