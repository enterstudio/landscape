<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />
<xsl:import href="banshee/splitform.xslt" />

<!--
//
//  Layout templates
//
//-->
<xsl:template name="splitform_header">
<h1>Title</h1>
<div class="progress">
	<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="{current + 1}" aria-valuemin="1" aria-valuemax="{current/@max + 2}" style="width:{current/@percentage}%"><xsl:value-of select="current + 1" /> / <xsl:value-of select="current/@max + 2" /></div>
</div>
</xsl:template>

<xsl:template name="splitform_footer">
</xsl:template>

<!--
//
//  Form template
//
//-->
<xsl:template match="splitform/template_name">
<label for="key1">Key 1:</label>
<input type="text" id="key1" name="key1" value="{key1}" class="form-control" />
<label for="key2">Key 2:</label>
<input type="text" id="key2" name="key2" value="{key2}" class="form-control" />
</xsl:template>

<!--
//
//  Process template
//
//-->
<xsl:template match="submit">
<xsl:call-template name="splitform_header" />
<p>Your information has been processed.</p>
<input type="button" value="Continue" class="button" onClick="javascript:document.location='/'" />
</xsl:template>

</xsl:stylesheet>
