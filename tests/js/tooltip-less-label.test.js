#!/usr/bin/env node
/**
 * Dependency-free regression: zero-month tooltips must use empty_zone_text, not zone_text.
 */
var fs = require('fs');
var path = require('path');
var vm = require('vm');

var jsPath = path.join(__dirname, '../../views/js/dashgoals.js');
var source = fs.readFileSync(jsPath, 'utf8');

var tooltipContent = null;

var chartModel = {
  stacked: function () {
    return chartModel;
  },
  showControls: function () {
    return chartModel;
  },
  tooltipContent: function (cb) {
    tooltipContent = cb;
    return chartModel;
  },
  yAxis: {
    tickFormat: function () {
      return chartModel;
    },
  },
  update: function () {},
};

function jqueryStub() {
  return {
    ready: function (fn) {
      if (fn) {
        fn();
      }
    },
    remove: function () {},
    each: function () {},
    attr: function () {
      return '';
    },
    text: function () {},
    val: function () {
      return '0';
    },
    keyup: function () {},
    off: function () {},
    hasClass: function () {
      return false;
    },
    replaceWith: function () {},
    removeClass: function () {},
  };
}

var sandbox = {
  dashgoals_data: null,
  dashgoals_chart: null,
  formatCurrency: function (n) {
    return String(n);
  },
  currency_format: 1,
  currency_sign: '€',
  currency_blank: 0,
  document: {},
  $: jqueryStub,
  d3: {
    select: function () {
      return {
        datum: function () {
          return this;
        },
        transition: function () {
          return this;
        },
        call: function () {
          return this;
        },
      };
    },
    format: function () {
      return function (v) {
        return v;
      };
    },
  },
  nv: {
    addGraph: function (fn) {
      fn();
    },
    models: {
      multiBarChart: function () {
        return chartModel;
      },
    },
    utils: {
      windowResize: function () {},
    },
  },
  console: console,
};

vm.runInNewContext(source, sandbox, { filename: jsPath });

sandbox.bar_chart_goals('dashgoals', { data: [] });

if (!tooltipContent) {
  console.error('tooltipContent callback was not registered');
  process.exit(1);
}

var metrics = ['traffic', 'conversion', 'avg_cart_value', 'sales'];
var failures = [];

function baseGraph(pointValue) {
  return {
    value: 1,
    series: {
      title: 'T',
      unit_text: '',
      zone_text: 'Goal not reached',
      empty_zone_text: 'Goal set:',
    },
    point: {
      goal: 10,
      goal_diff: -5,
      traffic: 0,
      conversion: 0,
      avg_cart_value: 0,
      sales: 0,
    },
  };
}

metrics.forEach(function (type) {
  var key = type + '_less';
  var graphZero = baseGraph(0);
  graphZero.point[type] = 0;
  var htmlZero = tooltipContent(key, null, null, graphZero);
  if (htmlZero.indexOf('Goal set:') < 0) {
    failures.push(key + ' zero month must show Goal set:');
  }
  if (htmlZero.indexOf('Goal not reached') >= 0) {
    failures.push(key + ' zero month must not show Goal not reached');
  }

  var graphPos = baseGraph(5);
  graphPos.point[type] = 5;
  var htmlPos = tooltipContent(key, null, null, graphPos);
  if (htmlPos.indexOf('Goal not reached') < 0) {
    failures.push(key + ' under-performing month must show Goal not reached');
  }
  if (htmlPos.indexOf('Goal set:') >= 0) {
    failures.push(key + ' under-performing month must not show Goal set:');
  }
});

if (failures.length) {
  console.error(failures.join('\n'));
  process.exit(1);
}

console.log('tooltip-less-label: ok');
