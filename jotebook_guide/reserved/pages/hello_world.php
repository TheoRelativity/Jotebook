<h1>Let's Start</h1>
<div class="quote">
We are going to make our first Jotebook. Go to your Jotebook root folder and enter into the the folder named papers.
Make a new folder named "my_jotebook". Enter inside it.
Make a new file named "elements.json".
Insert this code inside.

<?=
md("```

{
  \"canonicals\":[\"\"],
  
  
  \"info\": 
  {
	\"title\":      \"\",
	\"category\":   \"\",
  },


 \"index\":
 {
	
	
 },
 
 \"paragraphs\": 
  {
   
  }
}

```") ?>

This is the anatomy of each paper. You can compare the paper to a real paper where you write notes.
Insert the title and the author into the info section.
<?= md("```
\"title\":      \"Page Title\",
\"category\":   \"Category Name\"`
")?>

</div>

<div class="light-quote">
<?=
md("") ?></div>
