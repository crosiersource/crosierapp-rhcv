'use strict';

let listId = "#cvList";

import DatatablesJs from './crosier/DatatablesJs';

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';

Routing.setRoutingData(routes)

function getDatatablesColumns() {
    return [
        {
            name: 'e.nome',
            data: 'e.nome',
            title: 'Nome'
        },
        {
            name: 'e.cpf',
            data: 'e.cpf',
            title: 'CPF'
        },
        {
            name: 'e.dtNascimento',
            data: 'e.dtNascimento',
            title: 'Dt Nascimento',
            render: function (data, type, row) {
                console.log(data);
                return Moment(data).format('DD/MM/YYYY');
            },
            className: 'text-center'
        },
        {
            name: 'e.updated',
            data: 'e.updated',
            title: 'Atualizado em',
            render: function (data, type, row) {
                console.log(data);
                return Moment(data).format('DD/MM/YYYY');
            },
            className: 'text-center'
        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                if ($(listId).data('routeedit')) {
                    let routeedit = Routing.generate($(listId).data('routeedit'), {id: data.id});
                    colHtml += DatatablesJs.makeEditButton(routeedit);
                }
                if ($(listId).data('routedelete')) {
                    let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
                    let csrfTokenDelete = $(listId).data('crsf-token-delete');
                    colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
                }
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());