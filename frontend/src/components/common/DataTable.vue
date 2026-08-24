<template>

<table class="table table-hover">

<thead class="table-light">

<tr>

<th
v-for="column in columns"
:key="column">

{{column}}

</th>

</tr>

</thead>


<tbody>

<tr
v-for="row in data"
:key="row.id">


<td
v-for="value in row"
:key="value">

{{value}}

</td>


</tr>


</tbody>


</table>

</template>


<script setup>

defineProps({

columns:Array,

data:Array

})

</script>