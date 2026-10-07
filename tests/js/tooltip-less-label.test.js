#!/usr/bin/env node
/**
 * Dependency-free regression: zero-month tooltips must use empty_zone_text, not zone_text.
 */
var fs = require('fs');
var path = require('path');

var jsPath = path.join(__dirname, '../../views/js/dashgoals.js');
var source = fs.readFileSync(jsPath, 'utf8');

var branches = [
  'sales_less',
  'avg_cart_value_less',
  'traffic_less',
  'conversion_less',
];

var failures = [];
branches.forEach(function (key) {
  var marker = "key == '" + key + "'";
  var start = source.indexOf(marker);
  if (start < 0) {
    failures.push('missing branch ' + key);
    return;
  }
  var slice = source.slice(start, start + 600);
  if (slice.indexOf('graph.series.empty_zone_text') < 0) {
    failures.push(key + ' zero branch must reference graph.series.empty_zone_text');
  }
  var elsePos = slice.indexOf('else');
  if (elsePos < 0) {
    failures.push(key + ' missing else branch');
    return;
  }
  var elseSlice = slice.slice(elsePos, elsePos + 250);
  if (elseSlice.indexOf('graph.series.zone_text') >= 0) {
    failures.push(key + ' zero branch must not use zone_text');
  }
});

if (failures.length) {
  console.error(failures.join('\n'));
  process.exit(1);
}

console.log('tooltip-less-label: ok');
