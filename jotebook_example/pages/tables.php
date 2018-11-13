<h1>How To Write Tables in Jotebook</h1>

<h2>Simple Table</h2>
<?=
md('```"simple-table-example":
	{
		"type":"table",
		"canonicals": ["jotebook/examples/table"],
		"title"  : "Simple Table - Example",
		"references": [],
		"columns": [{"text":"Name"},{"text":"Surname"}],
		"rows"   : 
				  [
				    [{"text":"Bill"},{"text":"Gates"}],
					[{"text":"Albert"},{"text":"Einstein"}]
				  ]
	}

```') ?>

<?= paper("paragraphs/simple-table-example") ?>

<h2>MD Table</h2>
<?=
md('```	"md-table-example":
	{
		"type":"table",
		"canonicals": ["jotebook/examples/mdtable"],
		"title"  : "MarkedDown Table - Example",
		"references": [],
		"columns": [{"text":"Name"},{"text":"Surname"},{"text":"website"}],
		"rows"   : 
				  [
				    [{"text":"Bill"},{"text":"Gates"},{"text":"[Microsoft](https://www.microsoft.com/)","md":true}],
					[{"text":"Albert"},{"text":"Einstein"},{"text":"[Wiki](https://en.wikipedia.org/wiki/Albert_Einstein)","md":true}]
				  ]
	}
```') ?>

<?= paper("paragraphs/md-table-example") ?>

<h3>CSS Table</h2>
<?=
md('```	"css-table-example":
	{
		"type":"table",
		"canonicals": ["jotebook/examples/csstable"],
		"title"  : "Table with CSS class Applied - Example",
		"class": "data-value small-table",
		"references": [],
		"columns": [{"text":"Name"},{"text":"Surname"},{"text":"website"}],
		"rows"   : 
				  [
				    [{"text":"Bill"},{"text":"Gates"},{"text":"[Microsoft](https://www.microsoft.com/)","md":true}],
					[{"text":"Albert"},{"text":"Einstein"},{"text":"[Wiki](https://en.wikipedia.org/wiki/Albert_Einstein)","md":true}]
				  ]
	}
```') ?>

<?= paper("paragraphs/css-table-example") ?>

</div>
