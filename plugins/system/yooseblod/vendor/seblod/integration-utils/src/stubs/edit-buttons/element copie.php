<?php

$element	=	json_decode( 
'{
	"type": "row",
	"children":
	[
		{
			"type": "column",
			"props":
			{
				"image_position": "center-center",
				"position_sticky_breakpoint": "m"
			},
			"children":
			[
				{
					"type": "button",
					"props":
					{
						"class": "uk-hidden-hover",
						"grid_column_gap": "small",
						"grid_row_gap": "small",
						"margin_bottom": "default",
						"margin_top": "default",
						"position": "absolute",
						"position_top": "-40",
						"position_z_index": "3"
					},
					"children":
					[
						{
							"type": "button_item",
							"props":
							{
								"button_style": "",
								"class": "uk-icon-button uk-text-warning",
								"dialog_layout": "modal",
								"dialog_offcanvas_flip": true,
								"icon": "pencil",
								"icon_align": "left"
							},
                            "source":
                            {
                                "query":
                                {
                                    "name": "seblod"
                                },
                                "props":
                                {
                                    "link":
                                    {
                                        "name": "o_section_edit_link"
                                    }
                                }
                            },
							"name": "Edit button"
						},
						{
							"type": "button_item",
							"props":
							{
								"button_style": "",
								"class": "uk-icon-button uk-text-success",
								"dialog_layout": "modal",
								"dialog_offcanvas_flip": true,
								"icon": "yootheme",
								"icon_align": "left",
								"link": "'.$link_customizer.'"
							},
							"name": "Customizer button"
						}
					]
				}
			]
		}
	],
	"props":
	{
		"alignment": "left",
		"class": "nav-edit",
		"column_gap": "collapse",
		"html_element": "aside",
		"margin_bottom": "remove",
		"margin_top": "remove",
		"row_gap": "collapse"
	}
}' );